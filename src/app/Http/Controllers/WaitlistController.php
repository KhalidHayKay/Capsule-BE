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

        return response()->json([
            'status' => 'success',
            'data'   => $lists,
        ], 200);
    }

    public function store(CreateWaitlistRequest $request)
    {
        $data = $request->validated();

        $entry = Waitlist::create($data);

        $referralLink = config('frontend.url') . '/waitlist?ref=' . $entry->referral_code;

        Mail::to($entry->email)->send(new WaitlistConfirmation(
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
        return response()->json([
            'status' => 'sucsess',
            'data'   => $waitlist,
        ], 200);
    }

    public function handle()
    {
        //
    }
}
