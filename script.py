import os
import sys
from random import randint
with open('theatres.txt') as f:
	for line in f:
		for i in range(1,30):
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%La La Land (2016)%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%Logan (2017)%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%Moonlight (2016)%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%Fantastic Beasts and Where to Find Them (2016)%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%Moana%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%Kimi no na wa%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))
			print("INSERT INTO `arrange` (`ArrangeId`, `Showtime`, `Location`, `Name`, `SeatsLeft`) VALUES (NULL, '2017-{}-{} 00:00:00', (SELECT Location from theater WHERE Location LIKE '%{}%'), (SELECT Name from movie WHERE Name LIKE '%Mahouka Koukou no Rettousei Movie: Hoshi wo Yobu Shoujo%'), '300');".format(randint(3,12), i, line.split(";")[1].strip()))