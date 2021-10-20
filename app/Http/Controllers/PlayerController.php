<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlayerController extends Controller
{
    /**
     * Display a listing of the Player.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $players = Player::all()->map(function ($player) {
            return [
                'id' => $player->id,
                'handle' => $player->handle,
                'name' => $player->name(),
                'image_url' => $player->image_url,
                'path' => $player->path(),
            ];
        });

        return Inertia::render('Player/Index', [ 'players' => $players ]);
    }

    /**
     * Show the form for creating a new Player.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Player/Create');
    }

    /**
     * Store a newly created Player in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Player::create($request->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'handle' => ['required', 'max:50'],
            'image_url' => ['required', 'url'],
        ]));

        return redirect(route('players.index'));
    }

    /**
     * Display the specified Player.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Player.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified Player in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified Player from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
