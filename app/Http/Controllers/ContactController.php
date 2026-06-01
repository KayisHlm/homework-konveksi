<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    public function index(Request $request)
    {
        $contactsQuery = Contact::query();

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $contactsQuery->where(function ($query) use ($q) {
                $query
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        $contacts = $contactsQuery->orderBy('name')->get();

        return view('Page.Admin.Contact.Index', compact('contacts'));
    }
}
