<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Events/Index', [
            'events' => Event::with('user:id,name')->latest()->get(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Validation des données
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after:today',
            'location' => 'nullable|string|max:255',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_online' => 'required|boolean',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'nullable|string|in:draft,published,cancelled',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_info' => 'nullable|array',
        ]);

        // Si une nouvelle image est envoyée, la traiter
        if ($request->hasFile('banner_image')) {
            $bannerImage = $request->file('banner_image');

            // Supprimer l'ancienne image si elle existe
            $bannerImagePath = 'images/banner/' . $event->banner_image;

            if ($event->banner_image && Storage::disk('public')->exists($bannerImagePath)) {
                Storage::disk('public')->delete($bannerImagePath);
            }

            // Enregistrer la nouvelle image
            $bannerImagePath = $bannerImage->store('images/banner', 'public');
            $validatedData['banner_image'] = $bannerImagePath;
        }

        // Mettre à jour l'événement
        $event->update($validatedData);

        // Rediriger avec un message de succès
        return redirect(route('events.index'))->with('success', 'Event updated successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_online' => 'required|boolean',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3', // Par exemple USD, EUR, etc.
            'status' => 'nullable|string|in:draft,published,cancelled',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_info' => 'nullable|array',
        ]);

        // Si une image est envoyée, la traiter
        if ($request->hasFile('banner_image')) {
            $bannerImage = $request->file('banner_image');
            $bannerImagePath = $bannerImage->store('images/banner', 'public');
            $validatedData['banner_image'] = $bannerImagePath;
        }

        // Créer l'événement
        $request->user()->events()->create($validatedData);

        // Rediriger avec un message de succès
        return redirect(route('events.index'))->with('success', 'Event created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Supprimer l'image de la bannière si elle existe
        $bannerImagePath = 'images/banner/' . $event->banner_image;

        if ($event->banner_image && Storage::disk('public')->exists($bannerImagePath)) {
            Storage::disk('public')->delete($bannerImagePath);
        }

        // Supprimer l'événement
        $event->delete();

        // Rediriger l'utilisateur avec un message de succès
        return redirect()->route('events.index')->with('success', 'Événement supprimé avec succès.');
    }
}
