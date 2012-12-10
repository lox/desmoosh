Desmoosh
--------

Takes a string with spaces removed and infers word boundaries.

Requires a DAWG to be built initially:

```bash
$ cat ispell/english.* ispell/extra | php bin/buildgraph.php ispell/ispell.json
```

Then the desmoosh script references the graph built and stored in json:

```bash
$ cat examples.txt | php bin/desmoosh.php ispell/ispell.json

debtconsolidateweb => debt consolidate web (in 3.94ms)
mydisneyvacationresort => my disney vacation resort (in 4.72ms)
machoarts => macho arts (in 0.91ms)
idrawonphoto => id raw on photo (in 0.63ms)
myfreeforextraining => my free forex training (in 1.73ms)
cattick => cat tick (in 0.41ms)
fooarmrestyourbararrestbarfoo => foo armrest your bar arrest bar foo (in 9.98ms)
```

Damn you Dave for wasting my entire weekend.
