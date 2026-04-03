<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Category;
use App\Http\Requests\MenuRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use App\Http\Controllers\Traits\ImageHandlerTrait;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;
use App\Http\Controllers\Traits\OrderStatisticsTrait;

class MenuController extends Controller
{
    use AdminViewSharedDataTrait;
    use ImageHandlerTrait;
    use OrderStatisticsTrait;


    public function __construct()
    {
        $this->shareAdminViewData();
        $this->shareOrderStatistics();
        
    }
    
    public function index()
    {
        $categories = Category::with('menus')->get();
        $canManageMenuCrud = Auth::user()->role === 'global_admin';

        return view('admin.menus', compact('categories', 'canManageMenuCrud'));
    }

    public function store(MenuRequest $request)
    {
        $validated = $request->validated();
        
        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleImageUpload($validated['image'], "menus");
        }
    
        Menu::create($validated);
    
        return back()->with('success', 'Menu created successfully!');
    }
    

    public function update(MenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $validated = $request->validated();
    
        if ($request->hasFile('image')) {
            // Delete old image
            $imagePath = storage_path('app/public/' . ltrim($menu->image, '/'));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
    
            // Handle new image upload
            $validated['image'] = $this->handleImageUpload($validated['image'],"menus");
        }
    
        $menu->update($validated);
    
        return back()->with('success', 'Menu updated successfully!');
    }
    

 

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $imagePath = storage_path('app/public/' . ltrim($menu->image, '/'));


            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the image file
            }
     

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
    }

    public function toggleVisibility($id)
    {
        try {
            \Log::info('Toggle visibility called for menu ID: ' . $id . ', User role: ' . Auth::user()->role);

            if (!in_array(Auth::user()->role, ['global_admin', 'cashier'])) {
                \Log::warning('Unauthorized toggle visibility attempt by user: ' . Auth::user()->id);
                return redirect()->route('admin.menus.index')->with('error', 'You do not have permission to manage menu visibility.');
            }

            $menu = Menu::findOrFail($id);
            $menu->is_hidden = !$menu->is_hidden;
            $menu->save();

            $status = $menu->is_hidden ? 'hidden' : 'visible';
            
            \Log::info('Menu toggled successfully. User: ' . Auth::user()->id . ', Status: ' . $status);

            return redirect()->route('admin.menus.index', ['status' => $status, 'action' => 'toggle'])->with('success', 
                $menu->is_hidden 
                    ? 'Menu has been hidden from the customer website.'
                    : 'Menu is visible again on the customer website.'
            );
        } catch (\Exception $e) {
            \Log::error('Error toggling menu visibility: ' . $e->getMessage());
            return redirect()->route('admin.menus.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


}
