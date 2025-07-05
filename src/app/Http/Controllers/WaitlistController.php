<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWaitlistRequest;
use App\Mail\WaitlistConfirmation;
use App\Models\Waitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    public function index()
    {
        $lists = Waitlist::all(['email', 'name', 'referral_code']);

        return response()->json($lists);
    }

    public function store(CreateWaitlistRequest $request)
    {
        $request->validated();

        $entry = Waitlist::create([
            'email'       => $request->input('email'),
            'name'        => $request->input('name'),
            'referred_by' => $request->input('referred_by'),
        ]);

        $referralLink = config('frontend.url') . '/waitlist?ref=' . $entry->referral_code;

        Mail::to('mail@example.com')->send(new WaitlistConfirmation(
            $entry->name,
            $referralLink
        ));

        return response()->json([
            'message' => 'Waitlist entry created successfully',
            'entry'   => [
                    'name'  => $entry->name,
                    'email' => $entry->email,
                ],
        ], 201);
    }

    public function show(Waitlist $waitlist)
    {
        return response()->json($waitlist);
    }

    public function handle()
    {
        //
    }
}
