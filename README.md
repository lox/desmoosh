Desmoosh
--------

Takes a string with spaces removed and infers word boundaries.

Building a Word Graph
---------------------

Desmoosh works by finding all permutations of words and then choosing the best
possible combination based on frequency analysis from a corpus of text.

Before we can do this, the graph needs to be generated. This is slow, but once
it's done desmooshing is quick. The internal node tree is persisted to disk in JSON format.

I use [this word frequency](http://www.monlp.com/2012/04/16/calculating-word-and-n-gram-statistics-from-a-wikipedia-corpora/) table collected from Wikipedia and Project Gutenberg:

```bash
wget http://d241g0t0c5miix.cloudfront.net/data/combined_wordfreq.txt.gz
gunzip combined_wordfreq.txt.gz
head -n200000 combined_wordfreq.txt | php bin/buildgraph.php dict/wikipedia_guttenberg.json
```

Desmooshing words
-----------------

```bash
cat examples.txt | php bin/desmoosh.php dict/wikipedia_guttenberg.json

debtconsolidateweb => debtconsolidateweb => debt consolidate web (in 23.62ms)
mydisneyvacationresort => mydisneyvacationresort => my disney vacation resort (in 21.31ms)
machoarts => machoarts => macho arts (in 3.85ms)
idrawonphoto => idrawonphoto => i draw on photo (in 5.20ms)
myfreeforextraining => myfreeforextraining => my free forex training (in 10.20ms)
cattick => cattick => cat tick (in 1.46ms)
fooarmrestyourbararrestbarfoo => fooarmrestyourbararrestbarfoo => foo armrest your bar arrest bar foo (in 89.90ms)
expertsexchange => expertsexchange => experts exchange (in 10.64ms)
threelittlepigswenttomarket => threelittlepigswenttomarket => three little pigs went to market (in 189.31ms)
theirony => theirony => the irony (in 2.18ms)
malsucksatbugreports => malsucksatbugreports => mal sucks at bug reports (in 14.77ms)
```

