<?php

namespace App\Http\Controllers\TourGuide;

use App\Http\Controllers\Controller;
use App\Models\TourGuideAssignment;
use App\Services\TourGuideAssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(TourGuideAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Guide Dashboard
     */
    public function dashboard()
    {
        $guide = auth()->user()->tourGuide;
        $upcomingAssignments = $guide->acceptedAssignments()
            ->with(['booking.tour'])
            ->whereHas('booking', function ($q) {
                $q->where('tour_date', '>=', now());
            })
            ->orderBy('id', 'desc')
            ->get();

        $pendingAssignments = $guide->pendingAssignments()
            ->with(['booking.tour'])
            ->get();

        return view('tour-guide.dashboard', compact('guide', 'upcomingAssignments', 'pendingAssignments'));
    }

    /**
     * List all assignments
     */
    public function index()
    {
        $assignments = auth()->user()->tourGuide->assignments()
            ->with(['booking.tour'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('tour-guide.assignments-index', compact('assignments'));
    }

    /**
     * Show assignment details
     */
    public function show(Request $request, TourGuideAssignment $assignment)
    {
        // Verify the signed URL if coming from email
        if ($request->hasValidSignature()) {
            $assignment->load(['booking.tour', 'booking.customer', 'tourGuide']);

            return view('tour-guide.assignment-details', [
                'assignment' => $assignment,
                'booking' => $assignment->booking,
                'tour' => $assignment->booking->tour,
            ]);
        }

        abort(403, 'Invalid or expired link');
    }

    /**
     * Accept assignment (from email link or app)
     */
    public function accept(Request $request, TourGuideAssignment $assignment)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired link');
        }

        if (!$assignment->isPending()) {
            return redirect()->route('guide.assignment.show', $assignment)
                ->with('error', 'This assignment has already been responded to.');
        }

        try {
            $responseMethod = $request->has('email') ? 'email' : 'app';
            $this->assignmentService->acceptAssignment($assignment, $responseMethod);

            return view('tour-guide.assignment-accepted', [
                'assignment' => $assignment->fresh(['booking.tour']),
            ])->with('success', 'Assignment accepted! You will receive confirmation shortly.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to accept assignment: ' . $e->getMessage());
        }
    }

    /**
     * Show decline form
     */
    public function showDeclineForm(Request $request, TourGuideAssignment $assignment)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired link');
        }

        if (!$assignment->isPending()) {
            return redirect()->route('guide.assignment.show', $assignment)
                ->with('error', 'This assignment has already been responded to.');
        }

        return view('tour-guide.decline-assignment', [
            'assignment' => $assignment->load(['booking.tour']),
        ]);
    }

    /**
     * Decline assignment
     */
    public function decline(Request $request, TourGuideAssignment $assignment)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired link');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$assignment->isPending()) {
            return redirect()->route('guide.assignment.show', $assignment)
                ->with('error', 'This assignment has already been responded to.');
        }

        try {
            $responseMethod = $request->has('email') ? 'email' : 'app';
            $this->assignmentService->declineAssignment(
                $assignment,
                $request->reason,
                $responseMethod
            );

            return view('tour-guide.assignment-declined', [
                'assignment' => $assignment,
            ])->with('success', 'Thank you for your response. We will find another guide.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to decline assignment: ' . $e->getMessage());
        }
    }
}