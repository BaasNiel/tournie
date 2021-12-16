<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::all()->sortByDesc('id')->map(function ($game) {
            $gamePlayers = $game->players->map(function($player) {
                return [
                    'id' => $player->id,
                    'player_slot' => $player->pivot->player_slot,
                    'hero_id' => $player->pivot->hero_id,
                    'hero_level' => $player->pivot->hero_level,
                    'denies' => $player->pivot->denies,
                    'kills' => $player->pivot->kills,
                    'deaths' => $player->pivot->deaths,
                    'assists' => $player->pivot->assists,
                    'net_worth' => $player->pivot->net_worth,
                    'gold_per_minute' => $player->pivot->gold_per_minute,
                    'xp_per_minute' => $player->pivot->xp_per_minute,
                    'last_hits' => $player->pivot->last_hits,
                    'image_url' => $player->image_url
                ];
            });

            return [
                'id' => $game->id,
                'players' => $gamePlayers,
                'radiant_win' => $game->radiant_win,
                'start_time' => $game->start_time,
                'duration' => $game->human_readable_duration,
                'radiant_score' => $game->radiant_score,
                'dire_score' => $game->dire_score,
            ];
        })->values();

        return Inertia::render('Game/Index', [ 'games' => $games ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
