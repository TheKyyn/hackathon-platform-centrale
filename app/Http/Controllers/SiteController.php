<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    /**
     * Affiche la liste des sites.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sites = Site::withCount('leads')->get();
        return view('admin.sites.index', compact('sites'));
    }

    /**
     * Affiche le formulaire de création d'un site.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.sites.create');
    }

    /**
     * Stocke un nouveau site.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        // Générer un token API par défaut
        $validated['api_token'] = Str::random(60);

        $site = Site::create($validated);

        return redirect()->route('admin.sites.show', $site->id)
            ->with('success', 'Le site a été créé avec succès');
    }

    /**
     * Affiche les détails d'un site spécifique.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\View\View
     */
    public function show(Site $site)
    {
        $site->load(['leads' => function ($query) {
            $query->latest()->limit(5);
        }]);

        $leadsCount = $site->leads()->count();

        return view('admin.sites.show', compact('site', 'leadsCount'));
    }

    /**
     * Affiche le formulaire d'édition d'un site.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\View\View
     */
    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    /**
     * Met à jour le site spécifié.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // S'assurer que is_active est un booléen
        $validated['is_active'] = $request->has('is_active');

        $site->update($validated);

        return redirect()->route('admin.sites.show', $site->id)
            ->with('success', 'Le site a été mis à jour avec succès');
    }

    /**
     * Supprime le site spécifié.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Site $site)
    {
        // Cette action supprimera également tous les leads associés en raison de la contrainte onDelete cascade
        $site->delete();

        return redirect()->route('admin.sites.index')
            ->with('success', 'Le site et tous ses leads ont été supprimés avec succès');
    }

    /**
     * Régénère le token API pour un site.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateToken(Site $site)
    {
        $site->generateToken();

        return redirect()->route('admin.sites.show', $site->id)
            ->with('success', 'Le token API a été régénéré avec succès');
    }
}
