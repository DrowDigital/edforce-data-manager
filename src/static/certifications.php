<?php
/**
 * Admin Subcategories Form Template
 *
 * This file renders the HTML form for managing categories
 * within the EdForce Data Manager plugin.
 *
 * @package EdForceDataManager
 */

// Ensure this file is not accessed directly in a browser
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="msd-container">
    <h1>Manage Courses</h1>

    <!-- Add Subcategory Section -->
    <div class="msd-section">
        <h2>Add Subcategory under the Courses</h2>
        <button class="msd-button" onclick="toggleSubcategoryForm()">Add New Subcategory</button>
        <div id="subcategory-form" class="msd-form-content" style="display: none;">
            <form method="post" action="">
                <div class="msd-form-group">
                    <label for="subcategory_title">Title</label>
                    <input type="text" id="subcategory_title" name="subcategory_title" placeholder="Enter subcategory title">
                </div>
                <div class="msd-form-group">
                    <label for="subcategory_slug">Slug</label>
                    <input type="text" id="subcategory_slug" name="subcategory_slug" placeholder="Enter subcategory slug">
                </div>
                <div class="msd-form-group">
                    <label for="subcategory_description">Description</label>
                    <textarea id="subcategory_description" name="subcategory_description" placeholder="Enter subcategory description"></textarea>
                </div>
                <div class="msd-form-group">
                    <label for="subcategory_image">Image</label>
                    <input type="url" id="subcategory_image" name="subcategory_image" placeholder="Enter image URL">
                </div>
                <button type="submit" class="msd-button">Add Subcategory</button>
            </form>
        </div>
    </div>

    <!-- Tabs for Add Data and Show Data -->
    <div class="msd-section">
        <div class="msd-tabs">
            <button class="msd-tab-button active" onclick="switchTab('add-data')">Add Data</button>
            <button class="msd-tab-button" onclick="switchTab('show-data')">Show Data</button>
        </div>

        <!-- Add Data Section -->
        <div id="add-data" class="msd-tab-content active">
            <h2>Add Data</h2>
            <form method="post" action="">
                <div class="msd-form-group">
                    <label for="data_subcategory">Select Subcategory</label>
                    <select id="data_subcategory" name="data_subcategory">
                        <option value="">Choose a subcategory</option>
                        <!-- Subcategories would be dynamically populated here -->
                    </select>
                </div>
                <div class="msd-form-group">
                    <label for="data_title">Title</label>
                    <input type="text" id="data_title" name="data_title" placeholder="Enter data title">
                </div>
                <div class="msd-form-group">
                    <label for="data_image">Image</label>
                    <input type="url" id="data_image" name="data_image" placeholder="Enter image URL">
                </div>
                <div class="msd-form-group">
                    <label>Additional Data</label>
                    <div class="msd-additional-data">
                        <input type="text" name="data_key[]" placeholder="Enter key">
                        <input type="text" name="data_value[]" placeholder="Enter value">
                    </div>
                    <div class="msd-additional-data">
                        <input type="text" name="data_key[]" placeholder="Enter key">
                        <input type="text" name="data_value[]" placeholder="Enter value">
                    </div>
                </div>
                <button type="submit" class="msd-button">Add Data</button>
            </form>

            <!-- Upload Data Section -->
            <div class="msd-section">
                <h2>Upload Courses</h2>
                <div class="msd-file-upload">
                    <p>Drag and drop files here</p>
                    <p>Or click to select files</p>
                    <input type="file" id="bulk_upload" name="bulk_upload" multiple>
                    <button type="button" class="msd-button msd-button-secondary">Upload File</button>
                </div>
            </div>
        </div>

        <!-- Show Data Section -->
        <div id="show-data" class="msd-tab-content">
            <h2>Show Data</h2>
            <form method="post" action="">
                <div class="msd-form-group">
                    <label for="show_data_subcategory">Select Subcategory</label>
                    <select id="show_data_subcategory" name="show_data_subcategory">
                        <option value="">Choose a subcategory</option>
                        <option value="uncategorized">Uncategorized</option>
                        <!-- Subcategories would be dynamically populated here -->
                    </select>
                </div>
                <button type="submit" class="msd-button">Show Data</button>
            </form>
            <div id="data-display" class="msd-data-display">
                <!-- Data will be displayed here after submission -->
            </div>
        </div>
    </div>
</div>

<style>
    .msd-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    .msd-section {
        margin-bottom: 30px;
    }
    hultistyle: h1, h2 {
        color: #2c3e50;
    }
    .msd-form-group {
        margin-bottom: 15px;
    }
    .msd-form-group label {
        display: block;
        margin-bottom: 5px;
        color: #34495e;
    }
    .msd-form-group input,
    .msd-form-group textarea,
    .msd-form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #3498db;
        border-radius: 4px;
        background-color: #ecf0f1;
    }
    .msd-form-group textarea {
        height: 100px;
    }
    .msd-button {
        background-color: #2980b9;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .msd-button:hover {
        background-color: #3498db;
    }
    .msd-button-secondary {
        background-color: #7f8c8d;
    }
    .msd-button-secondary:hover {
        background-color: #95a5a6;
    }
    .msd-file-upload {
        border: 2px dashed #3498db;
        padding: 20px;
        text-align: center;
        background-color: #f5f7fa;
    }
    .msd-additional-data {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .msd-data-display {
        margin-top: 20px;
        padding: 10px;
        background-color: #f5f7fa;
        border: 1px solid #3498db;
        border-radius: 4px;
    }
    .msd-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .msd-tab-button {
        background-color: #ecf0f1;
        color: #34495e;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .msd-tab-button.active {
        background-color: #2980b9;
        color: white;
    }
    .msd-tab-button:hover {
        background-color: #3498db;
        color: white;
    }
    .msd-tab-content {
        display: none;
    }
    .msd-tab-content.active {
        display: block;
    }
    .msd-form-content {
        margin-top: 15px;
    }
</style>

<script>
    function toggleSubcategoryForm() {
        const form = document.getElementById('subcategory-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function switchTab(tabId) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.msd-tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        // Deactivate all tab buttons
        const tabButtons = document.querySelectorAll('.msd-tab-button');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });

        // Show the selected tab content
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }

        // Activate the clicked tab button
        const clickedButton = Array.from(tabButtons).find(button => button.getAttribute('onclick') === `switchTab('${tabId}')`);
        if (clickedButton) {
            clickedButton.classList.add('active');
        }
    }

    // Initialize the first tab as active on page load
    document.addEventListener('DOMContentLoaded', () => {
        switchTab('add-data');
    });
</script>