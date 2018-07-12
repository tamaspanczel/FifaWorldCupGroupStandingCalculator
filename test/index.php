<?php

include('../src/FifaWorldCupGroupStandingCalculator.php');

// https://en.wikipedia.org/wiki/2018_FIFA_World_Cup_Group_H

$teams = ['POL', 'SEN', 'COL', 'JAP'];
$matches = [
  [['team' => 'COL', 'goals' => 1, 'yellow' => 2, 'red' => 1, 'ired' => 0, 'yellowred' => 0, 'extra' => 0],
   ['team' => 'JAP', 'goals' => 2, 'yellow' => 1, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0]],

  [['team' => 'POL', 'goals' => 1, 'yellow' => 1, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0],
   ['team' => 'SEN', 'goals' => 2, 'yellow' => 2, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0]],

  [['team' => 'JAP', 'goals' => 2, 'yellow' => 2, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0],
   ['team' => 'SEN', 'goals' => 2, 'yellow' => 3, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0]],

  [['team' => 'POL', 'goals' => 0, 'yellow' => 2, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0],
   ['team' => 'COL', 'goals' => 3, 'yellow' => 0, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0]],

  [['team' => 'JAP', 'goals' => 0, 'yellow' => 1, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0],
   ['team' => 'POL', 'goals' => 1, 'yellow' => 0, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0]],

  [['team' => 'SEN', 'goals' => 0, 'yellow' => 1, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0],
   ['team' => 'COL', 'goals' => 1, 'yellow' => 1, 'red' => 0, 'ired' => 0, 'yellowred' => 0, 'extra' => 0]],
];

$group = [
  'teams' => $teams,
  'matches' => $matches,
];

$calc = new FifaWorldCupGroupStandingCalculator($group);
$result = $calc->getStanding();

// positions
assert((array_keys($result)[0] === 'COL'));
assert((array_keys($result)[1] === 'JAP'));
assert((array_keys($result)[2] === 'SEN'));
assert((array_keys($result)[3] === 'POL'));

// played
assert($result['COL']['played'] === 3);
assert($result['JAP']['played'] === 3);
assert($result['SEN']['played'] === 3);
assert($result['POL']['played'] === 3);

// won
assert($result['COL']['won'] === 2);
assert($result['JAP']['won'] === 1);
assert($result['SEN']['won'] === 1);
assert($result['POL']['won'] === 1);

// drawn
assert($result['COL']['drawn'] === 0);
assert($result['JAP']['drawn'] === 1);
assert($result['SEN']['drawn'] === 1);
assert($result['POL']['drawn'] === 0);

// lost
assert($result['COL']['lost'] === 1);
assert($result['JAP']['lost'] === 1);
assert($result['SEN']['lost'] === 1);
assert($result['POL']['lost'] === 2);

// goals for
assert($result['COL']['goals_for'] === 5);
assert($result['JAP']['goals_for'] === 4);
assert($result['SEN']['goals_for'] === 4);
assert($result['POL']['goals_for'] === 2);

// goals against
assert($result['COL']['goals_against'] === 2);
assert($result['JAP']['goals_against'] === 4);
assert($result['SEN']['goals_against'] === 4);
assert($result['POL']['goals_against'] === 5);

// goals diff
assert($result['COL']['goals_diff'] === 3);
assert($result['JAP']['goals_diff'] === 0);
assert($result['SEN']['goals_diff'] === 0);
assert($result['POL']['goals_diff'] === -3);

// fair play
assert($result['COL']['sort_fair'] === -7);
assert($result['JAP']['sort_fair'] === -4);
assert($result['SEN']['sort_fair'] === -6);
assert($result['POL']['sort_fair'] === -3);
