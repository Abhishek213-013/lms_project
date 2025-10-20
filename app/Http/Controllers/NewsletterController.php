<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscriptions,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // In production, you'd save to a newsletter_subscriptions table
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter!',
                'data' => [
                    'email' => $request->email,
                    'subscribed_at' => now()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe to newsletter',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}