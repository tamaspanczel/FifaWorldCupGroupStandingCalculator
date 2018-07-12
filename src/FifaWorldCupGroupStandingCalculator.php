<?php
/**
 * https://en.wikipedia.org/wiki/2018_FIFA_World_Cup#Group_stage
 * https://resources.fifa.com/image/upload/2018-fifa-world-cup-russiatm-regulations-2843519.pdf?cloudid=ejmfg94ac7hypl9zmsys
 *
 * sort_all:
 *
 * a) greatest number of points obtained in all group matches;
 * b) goal difference in all group matches;
 * c) greatest number of goals scored in all group matches.
 *
 * sort_concerned:
 *
 * d) greatest number of points obtained in the group matches between the teams concerned;
 * e) goal difference resulting from the group matches between the teams concerned;
 * f) greater number of goals scored in all group matches between the teams concerned;
 *
 * sort_fair:
 *
 * g) greater number of points obtained in the fair play conduct of the teams based on yellow and red cards received in all group matches as follows:
 * – yellow card: minus 1 point
 * – indirect red card: minus 3 points (as a result of a second yellow card)
 * – direct red card: minus 4 points
 * – yellow card and direct red card: minus 5 points
 * Only one of the above deductions shall be applied to a player in a single match;
 *
 * sort_orig:
 *
 * preserve original order
 *
 * sort_fifa:
 *
 * h) drawing of lots by FIFA
 *
 */

/**
 * Class FifaWorldCupGroupStandingCalculator
 */
class FifaWorldCupGroupStandingCalculator
{
    private $config = [
        'points' => [
            'won' => 3,
            'drawn' => 1,
            'lost' => 0,
            'yellow' => -1, // yellow card
            'red' => -4, // direct red card
            'ired' => -3, // indirect red card
            'yellowred' => -5, // yellow card and direct red card
        ],
        'keys' => [
            'teams' => 'teams',
            'matches' => 'matches',
            'team' => 'team',
            'goals' => 'goals',
            'yellow' => 'yellow',
            'red' => 'red',
            'ired' => 'ired',
            'yellowred' => 'yellowred',
            'extra' => 'extra',
        ],
        'standing' => [
            'played' => 0,
            'points' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goals_diff' => 0,
            'sort_all' => 0, // sort order of all group matches
            'sort_concerned' => 0, // sort order of concerned teams
            'sort_fair' => 0, // fair play
            'sort_orig' => 0, // preserve original order
            'sort_fifa' => 0, // fifa
        ],
    ];

    private $matches = [];
    private $teams = [];
    private $concerned = [];

    public function __construct($group)
    {
        $this->matches = $group[$this->config['keys']['matches']];
        $this->teams = $this->calculateTeams($group[$this->config['keys']['teams']], $this->matches);
        $this->calculateConcerned();
        $this->calculateTiebreakers();
        $this->sortTeams();
    }

    private function getPoint($goals1, $goals2)
    {
        if ($goals1 > $goals2) {
            return $this->config['points']['won'];
        }
        if ($goals1 < $goals2) {
            return $this->config['points']['lost'];
        }
        return $this->config['points']['drawn'];
    }

    private function calculateTeams($groupTeams, $matches)
    {
        // init
        $teams = [];
        foreach ($groupTeams as $index => $team) {
            $teams[$team] = $this->config['standing'];
            $teams[$team]['sort_orig'] = $index;
        }

        // matches
        foreach ($matches as $match) {
            $_team1 = $match[0][$this->config['keys']['team']];
            $_team2 = $match[1][$this->config['keys']['team']];
            $_goals1 = $match[0][$this->config['keys']['goals']];
            $_goals2 = $match[1][$this->config['keys']['goals']];

            // played
            $teams[$_team1]['played']++;
            $teams[$_team2]['played']++;

            // points
            $teams[$_team1]['points'] += $this->getPoint($_goals1, $_goals2);
            $teams[$_team2]['points'] += $this->getPoint($_goals2, $_goals1);

            // won
            $teams[$_team1]['won'] += ($_goals1 > $_goals2) ? 1 : 0;
            $teams[$_team2]['won'] += ($_goals2 > $_goals1) ? 1 : 0;

            // drawn
            $teams[$_team1]['drawn'] += ($_goals1 == $_goals2) ? 1 : 0;
            $teams[$_team2]['drawn'] += ($_goals2 == $_goals1) ? 1 : 0;

            // lost
            $teams[$_team1]['lost'] += ($_goals1 < $_goals2) ? 1 : 0;
            $teams[$_team2]['lost'] += ($_goals2 < $_goals1) ? 1 : 0;

            // goals for
            $teams[$_team1]['goals_for'] += $_goals1;
            $teams[$_team2]['goals_for'] += $_goals2;

            // goals against
            $teams[$_team1]['goals_against'] += $_goals2;
            $teams[$_team2]['goals_against'] += $_goals1;

            // goals diff
            $teams[$_team1]['goals_diff'] = $teams[$_team1]['goals_for'] - $teams[$_team1]['goals_against'];
            $teams[$_team2]['goals_diff'] = $teams[$_team2]['goals_for'] - $teams[$_team2]['goals_against'];

            // score/sort
            // greatest number of points
            // goal difference
            // greatest number of goals scored
            $teams[$_team1]['sort_all'] =
                (10000 * $teams[$_team1]['points']) +
                (100 * $teams[$_team1]['goals_diff']) +
                (1 * $teams[$_team1]['goals_for']);
            $teams[$_team2]['sort_all'] =
                (10000 * $teams[$_team2]['points']) +
                (100 * $teams[$_team2]['goals_diff']) +
                (1 * $teams[$_team2]['goals_for']);

            // fair play points
            $teams[$_team1]['sort_fair'] += $this->calculateFairPlayPoints($match[0]);
            $teams[$_team2]['sort_fair'] += $this->calculateFairPlayPoints($match[1]);

            // fifa
            $teams[$_team1]['sort_fifa'] = isset($match[0][$this->config['keys']['extra']]) ? $match[0][$this->config['keys']['extra']] : 0;
            $teams[$_team2]['sort_fifa'] = isset($match[1][$this->config['keys']['extra']]) ? $match[1][$this->config['keys']['extra']] : 0;
        }
        return $teams;
    }

    private function calculateConcerned()
    {
        foreach ($this->teams as $name => $team) {
            $this->concerned[$team['sort_all']][$name] = $name;
        }
    }

    private function calculateTiebreakers()
    {
        foreach ($this->concerned as $teams) {
            // if two or more teams are equal
            if (count($teams) > 1) {
                $concernedMatches = array_filter($this->matches, function ($match) use ($teams) {
                    $_team1 = $match[0][$this->config['keys']['team']];
                    $_team2 = $match[1][$this->config['keys']['team']];
                    return in_array($_team1, $teams) && in_array($_team2, $teams);
                });
                $concernedTeams = $this->calculateTeams($teams, $concernedMatches);
                foreach ($concernedTeams as $name => $team) {
                    $this->teams[$name]['sort_concerned'] = $team['sort_all'];
                }
            }
        }
    }

    private function calculateFairPlayPoints($match)
    {
        return
            ($this->config['points']['yellow'] * (isset($match[$this->config['keys']['yellow']]) ? $match[$this->config['keys']['yellow']] : 0)) +
            ($this->config['points']['red'] * (isset($match[$this->config['keys']['red']]) ? $match[$this->config['keys']['red']] : 0)) +
            ($this->config['points']['ired'] * (isset($match[$this->config['keys']['ired']]) ? $match[$this->config['keys']['ired']] : 0)) +
            ($this->config['points']['yellowred'] * (isset($match[$this->config['keys']['yellowred']]) ? $match[$this->config['keys']['yellowred']] : 0));
    }

    private function sortTeams()
    {
        uasort($this->teams, function ($_team1, $_team2) {
            if ($_team1['sort_all'] > $_team2['sort_all']) {
                return -1;
            }
            if ($_team1['sort_all'] < $_team2['sort_all']) {
                return 1;
            }
            if ($_team1['sort_concerned'] > $_team2['sort_concerned']) {
                return -1;
            }
            if ($_team1['sort_concerned'] < $_team2['sort_concerned']) {
                return 1;
            }
            if ($_team1['sort_fair'] > $_team2['sort_fair']) {
                return -1;
            }
            if ($_team1['sort_fair'] < $_team2['sort_fair']) {
                return 1;
            }
            if ($_team1['sort_orig'] > $_team2['sort_orig']) {
                return 1;
            }
            if ($_team1['sort_orig'] < $_team2['sort_orig']) {
                return -1;
            }
            if ($_team1['sort_fifa'] > $_team2['sort_fifa']) {
                return 1;
            }
            if ($_team1['sort_fifa'] < $_team2['sort_fifa']) {
                return -1;
            }
            return 0;
        });
    }

    public function getStanding()
    {
        return $this->teams;
    }
}
