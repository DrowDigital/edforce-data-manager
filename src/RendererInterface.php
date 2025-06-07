<?php

namespace Hp\EdforceDataManager\Interfaces;

interface RendererInterface{
    /**
     * Render the submenu page content.
     *
     * @return void
     */
    public function render_submenu_page($html);

    /**
     * Enqueue styles for the sumenupage page.
     *
     * @param string $hook_suffix The current admin page hook suffix.
     * @return void
     */
    public function enqueue_admin_styles($hook_suffix);

    /**
     * Enqueue scripts for the submenu page.
     *
     * @return void
     */
     public function enqueue_admin_scripts($hook_suffix);
}