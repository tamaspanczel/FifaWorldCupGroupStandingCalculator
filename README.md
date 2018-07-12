# FIFA World Cup Group Standing Calculator
#### Simple PHP script to calculate World Cup tiebreakers in group stage.
Resources:

 * https://resources.fifa.com/image/upload/2018-fifa-world-cup-russiatm-regulations-2843519.pdf?cloudid=ejmfg94ac7hypl9zmsys
 * https://en.wikipedia.org/wiki/2018_FIFA_World_Cup#Group_stage
 * https://en.wikipedia.org/wiki/2018_FIFA_World_Cup_Group_H

Supported tournaments:
 * 2018 FIFA World Cup
 
Usage:
```
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
$standing = $calc->getStanding();

```
