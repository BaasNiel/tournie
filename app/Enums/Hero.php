<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Str;

/**
 * @method static static Abaddon()
 * @method static static Alchemist()
 * @method static static AncientApparition()
 * @method static static AntiMage()
 * @method static static ArcWarden()
 * @method static static Axe()
 * @method static static Bane()
 * @method static static Batrider()
 * @method static static Beastmaster()
 * @method static static Bloodseeker()
 * @method static static BountyHunter()
 * @method static static Brewmaster()
 * @method static static Bristleback()
 * @method static static Broodmother()
 * @method static static CentaurWarrunner()
 * @method static static ChaosKnight()
 * @method static static Chen()
 * @method static static Clinkz()
 * @method static static Clockwerk()
 * @method static static CrystalMaiden()
 * @method static static DarkSeer()
 * @method static static DarkWillow()
 * @method static static Dazzle()
 * @method static static DeathProphet()
 * @method static static Disruptor()
 * @method static static Doom()
 * @method static static DragonKnight()
 * @method static static DrowRanger()
 * @method static static EarthSpirit()
 * @method static static Earthshaker()
 * @method static static ElderTitan()
 * @method static static EmberSpirit()
 * @method static static Enchantress()
 * @method static static Enigma()
 * @method static static FacelessVoid()
 * @method static static Gyrocopter()
 * @method static static Huskar()
 * @method static static Invoker()
 * @method static static Io()
 * @method static static Jakiro()
 * @method static static Juggernaut()
 * @method static static KeeperOfTheLight()
 * @method static static Kunkka()
 * @method static static LegionCommander()
 * @method static static Leshrac()
 * @method static static Lich()
 * @method static static Lifestealer()
 * @method static static Lina()
 * @method static static Lion()
 * @method static static LoneDruid()
 * @method static static Luna()
 * @method static static Lycan()
 * @method static static Magnus()
 * @method static static Mars()
 * @method static static Medusa()
 * @method static static Meepo()
 * @method static static Mirana()
 * @method static static MonkeyKing()
 * @method static static Morphling()
 * @method static static NagaSiren()
 * @method static static NaturesProphet()
 * @method static static Necrophos()
 * @method static static NightStalker()
 * @method static static NyxAssassin()
 * @method static static OgreMagi()
 * @method static static Omniknight()
 * @method static static Oracle()
 * @method static static OutworldDevourer()
 * @method static static Pangolier()
 * @method static static PhantomAssassin()
 * @method static static PhantomLancer()
 * @method static static Phoenix()
 * @method static static Puck()
 * @method static static Pudge()
 * @method static static Pugna()
 * @method static static QueenOfPain()
 * @method static static Razor()
 * @method static static Riki()
 * @method static static Rubick()
 * @method static static SandKing()
 * @method static static ShadowDemon()
 * @method static static ShadowFiend()
 * @method static static ShadowShaman()
 * @method static static Silencer()
 * @method static static SkywrathMage()
 * @method static static Slardar()
 * @method static static Slark()
 * @method static static Sniper()
 * @method static static Spectre()
 * @method static static SpiritBreaker()
 * @method static static StormSpirit()
 * @method static static Sven()
 * @method static static Techies()
 * @method static static TemplarAssassin()
 * @method static static Terrorblade()
 * @method static static Tidehunter()
 * @method static static Timbersaw()
 * @method static static Tinker()
 * @method static static Tiny()
 * @method static static TreantProtector()
 * @method static static TrollWarlord()
 * @method static static Tusk()
 * @method static static Underlord()
 * @method static static Undying()
 * @method static static Ursa()
 * @method static static VengefulSpirit()
 * @method static static Venomancer()
 * @method static static Viper()
 * @method static static Visage()
 * @method static static Warlock()
 * @method static static Weaver()
 * @method static static Windranger()
 * @method static static WinterWyvern()
 * @method static static WitchDoctor()
 * @method static static WraithKing()
 * @method static static Zeus()
 */
final class Hero extends Enum
{
    const Abaddon = 'Abaddon';
    const Alchemist = 'Alchemist';
    const AncientApparition = 'Ancient Apparition';
    const AntiMage = 'Anti-Mage';
    const ArcWarden = 'Arc Warden';
    const Axe = 'Axe';
    const Bane = 'Bane';
    const Batrider = 'Batrider';
    const Beastmaster = 'Beastmaster';
    const Bloodseeker = 'Bloodseeker';
    const BountyHunter = 'Bounty Hunter';
    const Brewmaster = 'Brewmaster';
    const Bristleback = 'Bristleback';
    const Broodmother = 'Broodmother';
    const CentaurWarrunner = 'Centaur Warrunner';
    const ChaosKnight = 'Chaos Knight';
    const Chen = 'Chen';
    const Clinkz = 'Clinkz';
    const Clockwerk = 'Clockwerk';
    const CrystalMaiden = 'Crystal Maiden';
    const DarkSeer = 'Dark Seer';
    const DarkWillow = 'Dark Willow';
    const Dazzle = 'Dazzle';
    const DeathProphet = 'Death Prophet';
    const Disruptor = 'Disruptor';
    const Doom = 'Doom';
    const DragonKnight = 'Dragon Knight';
    const DrowRanger = 'Drow Ranger';
    const EarthSpirit = 'Earth Spirit';
    const Earthshaker = 'Earthshaker';
    const ElderTitan = 'Elder Titan';
    const EmberSpirit = 'Ember Spirit';
    const Enchantress = 'Enchantress';
    const Enigma = 'Enigma';
    const FacelessVoid = 'Faceless Void';
    const Gyrocopter = 'Gyrocopter';
    const Huskar = 'Huskar';
    const Invoker = 'Invoker';
    const Io = 'Io';
    const Jakiro = 'Jakiro';
    const Juggernaut = 'Juggernaut';
    const KeeperOfTheLight = 'Keeper of the Light';
    const Kunkka = 'Kunkka';
    const LegionCommander = 'Legion Commander';
    const Leshrac = 'Leshrac';
    const Lich = 'Lich';
    const Lifestealer = 'Lifestealer';
    const Lina = 'Lina';
    const Lion = 'Lion';
    const LoneDruid = 'Lone Druid';
    const Luna = 'Luna';
    const Lycan = 'Lycan';
    const Magnus = 'Magnus';
    const Mars = 'Mars';
    const Medusa = 'Medusa';
    const Meepo = 'Meepo';
    const Mirana = 'Mirana';
    const MonkeyKing = 'Monkey King';
    const Morphling = 'Morphling';
    const NagaSiren = 'Naga Siren';
    const NaturesProphet = 'Nature’s Prophet';
    const Necrophos = 'Necrophos';
    const NightStalker = 'Night Stalker';
    const NyxAssassin = 'Nyx Assassin';
    const OgreMagi = 'Ogre Magi';
    const Omniknight = 'Omniknight';
    const Oracle = 'Oracle';
    const OutworldDevourer = 'Outworld Devourer';
    const Pangolier = 'Pangolier';
    const PhantomAssassin = 'Phantom Assassin';
    const PhantomLancer = 'Phantom Lancer';
    const Phoenix = 'Phoenix';
    const Puck = 'Puck';
    const Pudge = 'Pudge';
    const Pugna = 'Pugna';
    const QueenOfPain = 'Queen of Pain';
    const Razor = 'Razor';
    const Riki = 'Riki';
    const Rubick = 'Rubick';
    const SandKing = 'Sand King';
    const ShadowDemon = 'Shadow Demon';
    const ShadowFiend = 'Shadow Fiend';
    const ShadowShaman = 'Shadow Shaman';
    const Silencer = 'Silencer';
    const SkywrathMage = 'Skywrath Mage';
    const Slardar = 'Slardar';
    const Slark = 'Slark';
    const Sniper = 'Sniper';
    const Spectre = 'Spectre';
    const SpiritBreaker = 'Spirit Breaker';
    const StormSpirit = 'Storm Spirit';
    const Sven = 'Sven';
    const Techies = 'Techies';
    const TemplarAssassin = 'Templar Assassin';
    const Terrorblade = 'Terrorblade';
    const Tidehunter = 'Tidehunter';
    const Timbersaw = 'Timbersaw';
    const Tinker = 'Tinker';
    const Tiny = 'Tiny';
    const TreantProtector = 'Treant Protector';
    const TrollWarlord = 'Troll Warlord';
    const Tusk = 'Tusk';
    const Underlord = 'Underlord';
    const Undying = 'Undying';
    const Ursa = 'Ursa';
    const VengefulSpirit = 'Vengeful Spirit';
    const Venomancer = 'Venomancer';
    const Viper = 'Viper';
    const Visage = 'Visage';
    const Warlock = 'Warlock';
    const Weaver = 'Weaver';
    const Windranger = 'Windranger';
    const WinterWyvern = 'Winter Wyvern';
    const WitchDoctor = 'Witch Doctor';
    const WraithKing = 'Wraith King';
    const Zeus = 'Zeus';

    public static function startsWith($string): ?string {
        if (Str::length($string) < 2) {
            return null;
        }

        $names = self::getValues();
        foreach ($names as $name) {
            if (Str::startsWith(Str::lower($name), Str::lower($string))) {
                return $name;
            }
        }

        return null;
    }

    public static function findHeroName(array $strings): ?string {
        $parts = [];
        $name = null;

        foreach ($strings as $string) {
            $parts[] = $string;
            $startsWith = implode(' ', $parts);

            $nameCheck = self::startsWith($startsWith);
            if (is_null($nameCheck)) {
                break;
            }

            if (strtolower($nameCheck) == strtolower($startsWith)) {
                $name = $nameCheck;
            }
        }

        return $name;
    }
}
