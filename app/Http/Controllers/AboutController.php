<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the About Us page.
     */
    public function index(): View
    {
        $aboutTitle = Setting::get('about_title', 'About Jubilee Direct');
        $aboutSubtitle = Setting::get('about_subtitle', 'Connecting shoppers worldwide with global e-commerce stores.');
        $aboutContent = Setting::get('about_content', 'Jubilee Direct bridges the gap between international retailers and global shoppers.');
        $aboutMission = Setting::get('about_mission', 'To make global products accessible to anyone, anywhere by offering transparent pricing, secure procurement, and seamless doorstep delivery.');
        $aboutVision = Setting::get('about_vision', 'To become the premier global purchase-forwarding service trusted by millions for cross-border e-commerce solutions.');

        return view('about', compact(
            'aboutTitle',
            'aboutSubtitle',
            'aboutContent',
            'aboutMission',
            'aboutVision'
        ));
    }
}
