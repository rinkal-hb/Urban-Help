<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after:today',
            'time_slot' => 'required|string',
            'address' => 'required|string'
        ]);

        $service = Service::findOrFail($request->service_id);
        
        $booking = Booking::create([
            'customer_id' => Auth::id(),
            'service_id' => $request->service_id,
            'booking_date' => $request->booking_date,
            'time_slot' => $request->time_slot,
            'address' => $request->address,
            'total_amount' => $service->price,
            'status' => 'pending'
        ]);

        // Auto-assign to nearest provider
        $this->autoAssignProvider($booking);

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'message' => 'Booking created successfully'
        ]);
    }

    public function acceptBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->provider_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Booking accepted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:in_progress,completed']);
        
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function rateService(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $booking = Booking::where('id', $id)
            ->where('customer_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        $booking->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully'
        ]);
    }

    private function autoAssignProvider($booking)
    {
        $provider = User::where('role', 'provider')
            ->where('status', true)
            ->inRandomOrder()
            ->first();

        if ($provider) {
            $booking->update(['provider_id' => $provider->id]);
        }
    }
}