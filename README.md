DisguiseMe
==========

Disguise yourself as mobs or blocks for PocketMine-MP.


### Disguises
Disguises are different ways to conceal a Player. DisguiseMe comes with several builtin ones and new ones can be added. A disguise implements the `Disguise` interface and has a constructor which consumes a Player as the first argument and 1..* arguments (0 is possible, but causes some issues, see below) following. The disguise will be registered using a short name for command use.

### Command use
```
/d disguiseName param1:param2:param3 # Called by $player
__construct($player, "param1", "param2", "param3");
/d disguiseName param1:param2:param3 Emily # Where Emily is a valid username and coresponds to a Player, $emily
__construct($emily, "param1", "param2", "param3");
```

### Command
The /d command is both simple and very complicated. It allows for extensibility which adds a but of complexity. Some standard examples
 
```
/d m creeper # Disguise yourself using the mob Disguise as a creeper
/d id 37 # Disguise yourself using the id Disguise as a slime
/d m pig Greg # Disguise Greg as a pig using mob Disguise
/d # End your existing disguise
/d Greg # End Greg's existing disguise
```

Unfortunately there are some clashes in behaviour if you have the `disguiseme.other` permission and want to use a Disguise with no arguments.


Some examples of how to use it

```
/d dragon # Disguise yourself using the dragon disguse (if no Player is using the name "dragon")
/d -dragon # Disguise yourself using the dragon Disguise (always will work)
/d dragon -PlayerName # Disguise PlayerName using the dragon disguise

```
