<?php

namespace App\Services;

class SuperPasswordService
{
    /**
     * Complete word bank categorized by letter.
     *
     * @var array<string, list<string>>
     */
    protected const WORD_BANK = [
        'A' => ['Apple', 'Anchor', 'Airport', 'Astronaut', 'Ambulance', 'Alarm', 'Army', 'Artist', 'Attic', 'Armor'],
        'B' => ['Banana', 'Bridge', 'Bicycle', 'Butterfly', 'Backpack', 'Bakery', 'Balloon', 'Basket', 'Blanket', 'Bottle'],
        'C' => ['Camera', 'Castle', 'Candle', 'Carpet', 'Cinema', 'Cookie', 'Cactus', 'Compass', 'Circus', 'Crown'],
        'D' => ['Dolphin', 'Doctor', 'Diamond', 'Dragon', 'Dentist', 'Desert', 'Donkey', 'Drawer', 'Drum', 'Dinosaur'],
        'E' => ['Elephant', 'Engine', 'Eagle', 'Elevator', 'Envelope', 'Earring', 'Eclipse', 'Emerald', 'Escalator', 'Exam'],
        'F' => ['Forest', 'Football', 'Feather', 'Fireman', 'Fountain', 'Factory', 'Farmer', 'Fence', 'Flower', 'Fridge'],
        'G' => ['Guitar', 'Garden', 'Giraffe', 'Glasses', 'Ghost', 'Garage', 'Gold', 'Goat', 'Grape', 'Gloves'],
        'H' => ['Hospital', 'Hammer', 'Helicopter', 'Honey', 'Hotel', 'Harbor', 'Helmet', 'Hamburger', 'Horse', 'Hat'],
        'I' => ['Island', 'Igloo', 'Insect', 'Invitation', 'Internet', 'Ice cream', 'Iron', 'Ink', 'Inventor', 'Instrument'],
        'J' => ['Jungle', 'Jacket', 'Juice', 'Jellyfish', 'Judge', 'Jewelry', 'Jeans', 'Journal', 'Jigsaw', 'Jeep'],
        'K' => ['Kitchen', 'Kangaroo', 'Keyboard', 'Kettle', 'King', 'Kite', 'Key', 'Knife', 'Koala', 'Kindergarten'],
        'L' => ['Lemon', 'Ladder', 'Lighthouse', 'Library', 'Lion', 'Lamp', 'Lizard', 'Laptop', 'Luggage', 'Lightning'],
        'M' => ['Mountain', 'Mirror', 'Monkey', 'Museum', 'Microphone', 'Market', 'Magnet', 'Mailman', 'Mushroom', 'Medicine'],
        'N' => ['Notebook', 'Necklace', 'Nurse', 'Newspaper', 'Nest', 'Napkin', 'Needle', 'Neighbor', 'Noodle', 'Nose'],
        'O' => ['Orange', 'Ocean', 'Octopus', 'Orchestra', 'Office', 'Onion', 'Owl', 'Oven', 'Olive', 'Opera'],
        'P' => ['Pizza', 'Piano', 'Pirate', 'Penguin', 'Passport', 'Parrot', 'Pillow', 'Pencil', 'Pyramid', 'Pharmacy'],
        'R' => ['Rainbow', 'Robot', 'Rocket', 'Restaurant', 'River', 'Rabbit', 'Radio', 'Refrigerator', 'Ring', 'Railway'],
        'S' => ['Spider', 'Sandwich', 'School', 'Submarine', 'Soldier', 'Sofa', 'Stadium', 'Soap', 'Scissors', 'Skeleton'],
        'T' => ['Tiger', 'Telescope', 'Tractor', 'Teacher', 'Tunnel', 'Ticket', 'Towel', 'Turtle', 'Toothbrush', 'Train'],
        'W' => ['Whale', 'Window', 'Waiter', 'Waterfall', 'Wallet', 'Wizard', 'Wedding', 'Watch', 'Wolf', 'Witch'],
    ];

    /**
     * Get the full word bank.
     *
     * @return array<string, list<string>>
     */
    public function getWordBank(): array
    {
        return self::WORD_BANK;
    }

    /**
     * Get all available letters in the word bank.
     *
     * @return list<string>
     */
    public function getAvailableLetters(): array
    {
        return array_keys(self::WORD_BANK);
    }

    /**
     * Generate a new game round with random letters and words.
     *
     * @param  list<string>  $excludeWords
     * @return list<array{letter: string, word: string, status: string}>
     */
    public function generateRound(int $count = 10, array $excludeWords = []): array
    {
        $letters = $this->getAvailableLetters();
        shuffle($letters);

        $selectedLetters = array_slice($letters, 0, min($count, count($letters)));
        $items = [];

        foreach ($selectedLetters as $letter) {
            $pool = self::WORD_BANK[$letter] ?? [];
            $available = array_values(array_diff($pool, $excludeWords));

            if (empty($available)) {
                $available = $pool;
            }

            $chosenWord = $available[array_rand($available)];

            $items[] = [
                'letter' => $letter,
                'word' => $chosenWord,
                'status' => 'pending',
            ];
        }

        return $items;
    }
}
