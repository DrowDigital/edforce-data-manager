// Log EdForceData for debugging
console.log(EdForceData);
console.log(edforce_ajax_object);

function loadWidgetAssets(widgets, callback) {
    if (typeof elementorFrontend === 'undefined' || !widgets.length) {
        console.log("Elementor not found or no widgets to load");
        callback();
        return;
    }

    let loaded = 0;
    const total = widgets.length * 2; // JS + CSS per widget
    if (total === 0) {
        callback();
        return;
    }

    function checkAllLoaded() {
        loaded++;
        if (loaded === total) {
            console.log("All widget assets loaded:", widgets);
            callback();
        }
    }

    widgets.forEach(widget => {
        // Load JavaScript bundle
        const script = document.createElement('script');
        script.src = `/wp-content/plugins/elementor/assets/js/${widget}.bundle.min.js`;
        script.onload = () => {
            console.log(`Loaded JS for ${widget}`);
            checkAllLoaded();
        };
        script.onerror = () => {
            console.error(`Failed to load JS for ${widget}`);
            checkAllLoaded();
        };
        document.head.appendChild(script);

        // Load CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `/wp-content/plugins/elementor/assets/css/widget-${widget}.min.css?ver=3.28.4`;
        link.onload = () => {
            console.log(`Loaded CSS for ${widget}`);
            checkAllLoaded();
        };
        link.onerror = () => {
            console.error(`Failed to load CSS for ${widget}`);
            checkAllLoaded();
        };
        document.head.appendChild(link);
    });
}

// Parse HTML to extract widget types
function getWidgetsFromHtml(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const widgetElements = doc.querySelectorAll('[data-widget_type]');
    return [...new Set([...widgetElements].map(el => el.getAttribute('data-widget_type').split('.')[0]))];
}


// Client-side asset loader (fallback if server-side enqueuing isn't used)

class SNPContainer {
    constructor() {
        this.dataContainer = document.getElementById("snp-dynamic-data");
        this.dataHolder = this.dataContainer?.querySelector('.snp-dynamic-holder');
    }

    setSNPContainer() {
        if (!this.dataContainer || !this.dataHolder) {
            console.error("Missing DOM elements:", { dataContainer: this.dataContainer, dataHolder: this.dataHolder });
            return;
        }
    }

    isContainerSet() {
        if (!this.dataContainer || !this.dataHolder) {
            console.error("Missing DOM elements:", { dataContainer: this.dataContainer, dataHolder: this.dataHolder });
            return false;
        }
        return true;
    }

    getSNPContainer() {
        if (!this.dataContainer || !this.dataHolder) {
            console.error("Missing DOM elements:", { dataContainer: this.dataContainer, dataHolder: this.dataHolder });
            return;
        }
        return this.dataContainer;
    }

    getSNPHolder() {
        if (!this.dataContainer || !this.dataHolder) {
            console.error("Missing DOM elements:", { dataContainer: this.dataContainer, dataHolder: this.dataHolder });
            return;
        }
        return this.dataHolder;
    }
}

const snpContainer = new SNPContainer();


let isLoading = false; // Prevent multiple calls
let offset = 12; // For pagination or offset tracking
let limit = 24; // Number of items to load per request
let subcategory = 3; // For filtering by subcategory

function loadMoreData() {
    if (isLoading) return;
    isLoading = true;
    
    if (EdForceData.data_count <= offset) {
        console.log("No more data to load");
        isLoading = false;
        return;
    }
    // Simulate fetch delay (remove this in real use)
    setTimeout(async () => {
        // Your fetch logic goes here
        console.log("Fetching data from server...");
        // load data as per offset and limit
        
        const response = await sendRequest(`category_id=${EdForceData.category.id}&limit=${limit}&offset=${offset}`);
        console.log("Response received:", response);
        // After loading is done
        if (response) {
            insertInCard(response);
        }

        offset += limit;
        isLoading = false;
    }, 0);
}

async function sendRequest(query) {
    try {
        const response = await fetch(`${edforce_ajax_object.ajax_url}?action=load_edforce_data&${query}`);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        if (!data || !data.success) {
            console.error("No data returned or error in response");
            isLoading = false;
            return;
        }
        return data.data;
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

function handleScroll() {
    const scrollTop = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.body.offsetHeight;

    const scrollPercentage = (scrollTop + windowHeight) / documentHeight;
	
    if (scrollPercentage > 0.6 && EdForceData.data) {
        loadMoreData();
    }
}

function sum() {
    return 1  + 1;
}

function loadDataByQuery(){
    document.getElementById('searchButton').addEventListener('click', async function () {
        const searchInput = document.getElementById('searchInput').value;
        if (!searchInput.trim()) {
            console.warn("Search query is empty");
            return;
        }
        const query = `title=${searchInput.trim()}`;
       
        const data = await sendRequest(query);
        insertInCard(data, true);
    });
}


function insertData() {
    // Select DOM elements
    if (!snpContainer.isContainerSet()) {
        console.error("SNPContainer is not set correctly");
        return;
    }
    
    // Validate DOM elements
        
    if (EdForceData.type === "data") {
        const entry = EdForceData.data;
        insertDataForSingle(entry);
        return;
    }

    // Handle non-"data" type
    const data = EdForceData.data;
    if (!data || data.length === 0) {
        insertDataBySubcategory();
        return;
    } else {
        insertInCard(data);
    }

    // Re-init Elementor for non-"data" type
    if (window.elementorFrontend && elementorFrontend.init) {
        elementorFrontend.init();
        window.addEventListener('scroll', handleScroll);
        loadDataByQuery();
    }
}

async function loadTemplate(templateId){
    try {
        const response = await fetch(`${edforce_ajax_object.ajax_url}?action=get_edforce_template&template_id=${templateId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        if (!data || !data.success) {
            console.error("No data returned or error in response");
            return;
        }
        return data;
    } catch (error) {
        console.error("Error loading template:", error);
    }
}

function insertDataForSingle(entry) {
    const container = snpContainer.getSNPContainer();
    const holder = snpContainer.getSNPHolder();     

    document.querySelector(`[data-replacer="title"]`).querySelector('p').textContent = entry.title;
    const content = JSON.parse(entry.content || '{}');
    const dataToInsert = content.normal;
    const orderToInsert = content.order || [];
    const normal = content.normal;
    const templateContent = JSON.parse(entry.template_based_content || '{}');
    const merged = {
    ...normal,
    ...templateContent
    };
	document.querySelector(`[data-replacer="image"]`).querySelector('img').setAttribute('src', merged.image || '');
    orderToInsert.forEach(order => {
        // first type of data
        // second data key value
        if (order[0] === 0){
            insertNormalData(order[1]);
        } else if (order[0] === 1) {
			insertTemplateData(order[1]);
        } else {
            console.warn(`Unknown data type: ${order[0]}`);
        }
    });
	
	
	function kebabToTitleCase(str) {
		return str.split(/[-_]/) // Split on both hyphens and underscores
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');                          // Join with space
	}
            

    function insertNormalData(key) {
		if (key === "description" || key === "course-type") {
            document.querySelector(`[data-replacer="${key}"]`).querySelector('p').textContent = merged[key] || '';
            return;
        }
		
		if (key === "image") {
			console.log(key);
            document.querySelector(`[data-replacer="image"]`).querySelector('img').setAttribute('src', merged[key] || '');
            return;
        }


        const cloneHolder = holder.cloneNode(true);
        const el = cloneHolder.querySelector(`[data-replacer="key"]`);
        const valueEl = cloneHolder.querySelector(`[data-replacer="value"]`);

        
        if (!el) {
            console.warn(`Element with data-replacer="key" not found`);
            return;
        }
        if (!valueEl) {
            console.warn(`Element with data-replacer="value" not found`);
            return;
        }

        el.querySelector('p').textContent = kebabToTitleCase(key);
        valueEl.querySelector('p').innerHTML = merged[key] || '';

        container.appendChild(cloneHolder);
    }
    
	function insertTemplateData(key) {
		const cloneHolder = holder.cloneNode(true);
		const el = cloneHolder.querySelector(`[data-replacer="key"]`);
        const valueEl = cloneHolder.querySelector(`[data-replacer="value"]`);
		el.querySelector('p').textContent = kebabToTitleCase(key);
		loadTemplate(merged[key].template_id).then(response => {
			if (!response || !response.success) {
				console.error("Failed to load template:", response);
				return;
			}

			const templateHtml = response.data.content;
			const parser = new DOMParser();
			const doc = parser.parseFromString(templateHtml, 'text/html');

			// Optional: clean the clone holder first
			valueEl.innerHTML = '';

			// Append all children (not just firstChild)
			Array.from(doc.body.children).forEach(child => {
				valueEl.appendChild(child.cloneNode(true));
			});
			const contentToInsert = merged[key].data;
			const templateContentContainer = valueEl.querySelector('.snp-template-based-content');
			const templateContentHolder = templateContentContainer.querySelector('div');
			
			for (const [key, value] of Object.entries(contentToInsert)){
			    const cloneTemplateContentHolder = templateContentHolder.cloneNode(true);
				cloneTemplateContentHolder.querySelector('[data-replacer=key]').innerText = key;
				const valueHolder = cloneTemplateContentHolder.querySelector('[data-replacer=value]');
				const valueParser = new DOMParser();
				const _doc = valueParser.parseFromString(value, 'text/html');
				Array.from(_doc.body.children).forEach(child => {
					valueHolder.appendChild(child.cloneNode(true));
				});
				templateContentContainer.appendChild(cloneTemplateContentHolder);
			}
			templateContentHolder.style.display = "none";
			container.appendChild(cloneHolder); // now content is in the DOM
			
			// Now execute the scripts
			const scripts = doc.querySelectorAll('script');
			scripts.forEach(oldScript => {
				const newScript = document.createElement('script');

				if (oldScript.src) {
					newScript.src = oldScript.src;
					newScript.async = false; // maintain execution order
				} else {
					newScript.textContent = oldScript.textContent;
				}

				document.body.appendChild(newScript);
			});
		});
	}
	holder.style.display = "none";
	console.log(orderToInsert);
}


function insertDataBySubcategory() {
    const container = snpContainer.getSNPContainer();
    const holder = snpContainer.getSNPHolder();
    if (!container || !holder) {
        console.error("Missing DOM elements:", { container, holder });
        return;
    }

    const subcategories = EdForceData.subcategories || [];
    const subcategory_image = container.querySelector('[data-replacer="subcategory_image"]');
    const subcategory_holder = container.querySelector('.snp-subcategory-holder');
    const subcategory_title = container.querySelector('[data-replacer="subcategory_title"]');
	const subcategory_triggers = document.querySelector(".edforce-subcategories") || null;
	const subcategory_trigger_image_container = subcategory_triggers?.querySelector("div");
    if (subcategories.length === 0) {
        alert("No subcategories found");
        return;
    }
    subcategories.forEach(subcategory => {
		const cloneImage = subcategory_image.cloneNode(true);
		cloneImage.querySelector('img').setAttribute("src", JSON.parse(subcategory.subcategory.content).image);
		cloneImage.setAttribute("id", subcategory.subcategory.subcategory_uid);
		const clone = subcategory_holder.cloneNode(true);
		clone.style.scrollMarginTop = "70px";
		const triggerClone = subcategory_trigger_image_container?.cloneNode(true);
		triggerClone?.setAttribute("data-scroll-to", subcategory.subcategory.subcategory_uid);
		triggerClone?.querySelector('img').setAttribute("src", JSON.parse(subcategory.subcategory.content).image);
        insertInCard(subcategory.data, true, clone);
		container.appendChild(cloneImage);
        container.appendChild(clone);
		if (triggerClone) {
			triggerClone.addEventListener("click", () => {
				clone.scrollIntoView({
					behavior: 'smooth',
                	block: 'start'
				});
			});			
			subcategory_triggers?.appendChild(triggerClone);
		}
		
    });
	container.children[3].children[0].style.display = "none";
	subcategory_holder.style.display = "none";
	subcategory_image.style.display = "none";

	const amountElement = document.querySelectorAll('[data-replacer="price"]');
    amountElement.forEach(amount => {
		const _amount = amount.querySelector('p');
		const rawAmount = parseInt(_amount.textContent); // Get the number as a float
		console.log(rawAmount);
    	if (!isNaN(rawAmount)) {
        	_amount.textContent = rawAmount.toLocaleString('en-IN', {
            	style: 'currency',
            	currency: 'INR',
				minimumFractionDigits: 0, // Ensures at least 0 decimal places
        		maximumFractionDigits: 0 
        });
		}
	});
	subcategory_trigger_image_container.style.display = "none";
}



function insertMoreData() {
    // Check if SNPContainer is set
    const snpContainer = new SNPContainer();
    if (!snpContainer.isContainerSet()) {
        console.error("SNPContainer is not set correctly");
        return;
    }

    // Check if dataHolder exists
    const dataHolder = snpContainer.getSNPHolder();
    if (!dataHolder) {
        console.error("Data holder element not found");
        return;
    }
    
}


// Run after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    insertData();
});

function loadForSingle() {

}

function insertInCard(data, replace=false, container = snpContainer.getSNPContainer()) {
    const dataContainer = container; 
    const dataHolder = snpContainer.getSNPHolder();
    
    if (!dataContainer || !dataHolder ) {
        console.error("Missing DOM elements:", { dataContainer, dataHolder});
        return;
    }

	if (replace == true){
		while (dataContainer.children.length > 1) {
			dataContainer.removeChild(dataContainer.lastElementChild);
		}
	}
	
	const subcategories = EdForceData.subcategories || [];

    data?.forEach(element => {
        const content = JSON.parse(element.content);
        const clone = dataHolder.cloneNode(true);
        
		

        const titleEl = clone.querySelector(`[data-replacer="title"]`);
        if (titleEl && element.title) {
            titleEl.querySelector('p').textContent = element.title;
        }

		const imgEl = clone.querySelector(`[data-replacer="image"]`);
		

		
		if (element.subcategory_id != null && imgEl) {
			
			if (subcategories.length > 0) {
				const subcategory = subcategories.find(sub => sub.subcategory.id == element.subcategory_id);
				
				const subcategoryContent = subcategory ? JSON.parse(subcategory.subcategory.content) : null;
				console.log(subcategoryContent);
				if (subcategory && subcategoryContent.image) {
					const img = imgEl.querySelector('img');
					if (img) {
						img.setAttribute('src', subcategoryContent.image);
					}
				} else {
					console.warn(`Subcategory with ID ${element.subcategrory_id} not found`);
				}
			}
		}
        
        Object.entries(content).forEach(([key, value]) => {
            const el = clone.querySelector(`[data-replacer="${key}"]`);
            if (!el) return;
            
            if (key === "image") {
                const img = el.querySelector("img");
                if (img) img.setAttribute('src', value);
            } else {
                el.querySelector('p').textContent = value;
            }
        });

        clone.style.display = "flex";
        dataContainer.appendChild(clone);
    });
	dataHolder.style.display = "none"; 
}  