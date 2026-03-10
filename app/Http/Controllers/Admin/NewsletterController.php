<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(50);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed successfully.');
    }

    public function toggle(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        return back()->with('success', 'Subscriber status updated.');
    }
}
