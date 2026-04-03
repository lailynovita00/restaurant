<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Testimony;
use Illuminate\Http\Request;
use App\Models\OrderSettings;
use App\Models\PrivacyPolicy;
use App\Models\LiveChatScript;
use App\Helpers\DistanceHelper;
use App\Models\CompanyAddress;
use App\Models\SocialMediaHandle;
use App\Models\TermsAndCondition;
use App\Models\RestaurantPhoneNumber;
use App\Models\CompanyWorkingHour;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Traits\CartTrait;
use App\Http\Requests\CustomerDetailsRequest;
use App\Http\Controllers\Traits\OrderNumberGeneratorTrait;
use App\Http\Controllers\Traits\MainSiteViewSharedDataTrait;


class MainSiteController extends Controller
{
    use CartTrait;
    use MainSiteViewSharedDataTrait;
    use OrderNumberGeneratorTrait;


    public function __construct()
    {
        $this->shareMainSiteViewData();
    }

    public function home()
    {


        $menus = Menu::visible()->inRandomOrder()->get();
        $blogs = Blog::orderBy('created_at', 'desc')->limit(3)->get();
        $testimonies = Testimony::inRandomOrder()->limit(5)->get();




        return view('main-site.index', compact('menus','blogs','testimonies'));
    }

    public function about()
    {
        return view('main-site.about');
    }
    public function contact()
    {
        $addresses = CompanyAddress::all();
        $phoneNumbers = RestaurantPhoneNumber::all();
        $workingHours = CompanyWorkingHour::all();
    
        return view('main-site.contact', [ 'addresses' => $addresses, 'phoneNumbers' => $phoneNumbers, 'workingHours' => $workingHours, ]);
    }
    

    public function menu(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $hasSauceTables = Schema::hasTable('sauces') && Schema::hasTable('category_sauce');
        $hasSideTables = Schema::hasTable('sides') && Schema::hasTable('category_side');

        $query = Category::with(['menus' => function ($query) use ($request) {
            $query->visible();

            if ($request->has('search') && $request->search != '') {
                $query->where(function ($menuQuery) use ($request) {
                    $menuQuery->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('name_ar', 'like', '%' . $request->search . '%');
                });
            }
        }]);

        if ($hasSauceTables) {
            $query->with(['sauces' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
            }]);
        }

        if ($hasSideTables) {
            $query->with(['sides' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
            }]);
        }
    
        $categories = $query->get();
    
        return view('main-site.menu', compact('categories', 'hasSauceTables', 'hasSideTables'));
    }
    

    public function menuItem($id)
    {
        $hasSauceTables = Schema::hasTable('sauces') && Schema::hasTable('category_sauce');
        $hasSideTables = Schema::hasTable('sides') && Schema::hasTable('category_side');

        if ($hasSauceTables || $hasSideTables) {
            $with = ['category'];

            if ($hasSauceTables) {
                $with['category.sauces'] = function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
                };
            }

            if ($hasSideTables) {
                $with['category.sides'] = function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
                };
            }

            $menu = Menu::visible()->with($with)->findOrFail($id);

            if ($menu->category && !$hasSideTables) {
                $menu->category->setRelation('sides', collect());
            }

            if ($menu->category && !$hasSauceTables) {
                $menu->category->setRelation('sauces', collect());
            }
        } else {
            $menu = Menu::visible()->with(['category'])->findOrFail($id);

            if ($menu->category) {
                $menu->category->setRelation('sauces', collect());
                $menu->category->setRelation('sides', collect());
            }
        }
        $cart = session()->get($this->cartkey, []);

        function getItemQuantity($cart, $itemId) {
            foreach ($cart as $item) {
                if ($item['id'] == $itemId) {
                    return $item['quantity'];
                }
            }
            return 0; // Return 0 if item is not found
        }
        
        // Usage example
        $quantity = getItemQuantity($cart, $id);
        
    
    
        // Fetch 5 random related menus  
        $relatedMenus = Menu::visible()->where('id', '!=', $id)->inRandomOrder()->limit(5)->get();
    
        return view('main-site.menu-item', compact('menu','quantity', 'relatedMenus'));
    }
    

    public function cart()
    {
        return view('main-site.cart');
    }




    public function blogs(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);
    
        $query = Blog::query();
    
        // Check if there's a search query
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')->orWhere('content', 'like', '%' . $request->search . '%');
        }
    
        $blogs = $query->paginate(10);
    
        return view('main-site.blogs', compact('blogs'));
    }
    
    public function blogView($id)
    {
        $blog = Blog::findOrFail($id);

        $relatedBlogs = Blog::where('id', '!=', $id)->inRandomOrder()->limit(5)->get();

        return view('main-site.blog-view', compact('blog','relatedBlogs'));
    }

    public function login()
    {
        return view('main-site.login');
    }


    public function privacyPolicy()
    {
        $privacyPolicy  = PrivacyPolicy::latest()->first();
        return view('main-site.privacy-policy',compact('privacyPolicy'));
    }
    public function termsConditions()
    {
        $termsAndCondition = TermsAndCondition::latest()->first();
        return view('main-site.terms-conditions', compact('termsAndCondition'));
     }
 

    
}
