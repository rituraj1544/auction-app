<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExtraAuctionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Make sure extra categories exist ─────────────────────────────────
        $catNames = [
            'Electronics', 'Watches', 'Sneakers', 'Cars', 'Gaming',
            'Luxury', 'Cameras', 'Collectibles', 'Smartphones', 'Laptops',
            'Headphones', 'Tablets', 'Furniture',
        ];

        $cats = [];
        foreach ($catNames as $name) {
            $cats[$name] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // ── Grab existing users (bidders & sellers) ───────────────────────────
        $users = User::where('role', '!=', 'admin')->get();

        if ($users->count() < 3) {
            $this->command->error('Run the main DatabaseSeeder first to create users.');
            return;
        }

        // ── 70 realistic product definitions ────────────────────────────────
        $items = [
            // --- Smartphones ---
            ['Apple iPhone 15 Pro Max 512GB - Deep Purple',            'Smartphones',   850,  30, 'Like New',      ['Storage'=>'512GB','Battery Health'=>'99%','Network'=>'Unlocked','Includes'=>'Box, cable, earpods']],
            ['Samsung Galaxy S24 Ultra 256GB - Titanium Black',        'Smartphones',   720,  25, 'Excellent',     ['Storage'=>'256GB','RAM'=>'12GB','Screen'=>'6.8" QHD+','Includes'=>'Original box']],
            ['Google Pixel 8 Pro 128GB - Obsidian',                    'Smartphones',   480,  20, 'Excellent',     ['Storage'=>'128GB','Camera'=>'50MP triple','OS'=>'Android 14','Battery'=>'5050mAh']],
            ['OnePlus 12 256GB - Silky Black',                         'Smartphones',   390,  15, 'Like New',      ['Storage'=>'256GB','RAM'=>'16GB','Charging'=>'100W SUPERVOOC','Display'=>'6.82" LTPO']],
            ['Apple iPhone 14 128GB - Midnight',                       'Smartphones',   540,  20, 'Very Good',     ['Storage'=>'128GB','Battery Health'=>'91%','Chips'=>'A15 Bionic','Face ID'=>'Working']],

            // --- Laptops ---
            ['MacBook Air M2 13-inch 8GB/256GB - Midnight',            'Laptops',       850,  30, 'Like New',      ['Chip'=>'Apple M2','RAM'=>'8GB','Storage'=>'256GB SSD','Battery Cycles'=>'38']],
            ['Dell XPS 15 OLED - Intel i9 / RTX 4070',                'Laptops',       1100, 50, 'Excellent',     ['CPU'=>'Intel i9-13900H','GPU'=>'RTX 4070','RAM'=>'32GB','Storage'=>'1TB SSD']],
            ['Microsoft Surface Pro 9 - Graphite',                     'Laptops',       780,  25, 'Like New',      ['CPU'=>'Intel i7','RAM'=>'16GB','Storage'=>'512GB','Display'=>'13" PixelSense']],
            ['Lenovo ThinkPad X1 Carbon Gen 11',                       'Laptops',       920,  30, 'Excellent',     ['CPU'=>'Intel i7-1365U','RAM'=>'16GB','Storage'=>'512GB','Weight'=>'1.12kg']],
            ['ASUS ROG Zephyrus G14 - AMD Ryzen 9',                   'Laptops',       1050, 40, 'Like New',      ['CPU'=>'Ryzen 9 7940HS','GPU'=>'RTX 4060','RAM'=>'16GB','Display'=>'14" QHD 165Hz']],
            ['HP Spectre x360 14-inch - Nightfall Black',              'Laptops',       740,  25, 'Very Good',     ['CPU'=>'Intel i7','RAM'=>'16GB','Storage'=>'512GB','Touchscreen'=>'Yes 2-in-1']],

            // --- Cameras ---
            ['Sony Alpha A7 IV Mirrorless Body Only',                  'Cameras',       1800, 50, 'Like New',      ['Sensor'=>'33MP BSI CMOS','ISO'=>'100-51200','Video'=>'4K 60fps','Shutter Count'=>'3,200']],
            ['Nikon Z6 III with 24-70mm Z Lens',                      'Cameras',       2100, 75, 'Excellent',     ['Sensor'=>'24.5MP','Lens'=>'24-70mm f/2.8 S','Video'=>'6K RAW','Shutter Count'=>'7,800']],
            ['Fujifilm X-T5 Body - Black',                             'Cameras',       1350, 50, 'Like New',      ['Sensor'=>'40MP X-Trans','IBIS'=>'7-stop','Video'=>'6.2K','Shutter Count'=>'1,100']],
            ['Canon EOS R6 Mark II Body',                              'Cameras',       1950, 75, 'Excellent',     ['Sensor'=>'24.2MP','AF'=>'Dual Pixel CMOS II','Video'=>'4K 60p','Shutter Count'=>'4,500']],
            ['Leica Q3 Compact Full-Frame Camera',                     'Cameras',       4200, 150,'Like New',      ['Sensor'=>'60MP','Lens'=>'28mm Summilux f/1.7','Video'=>'4K HDR','Box'=>'Complete']],
            ['GoPro Hero 12 Black Bundle',                             'Cameras',       280,  15, 'Like New',      ['Video'=>'5.3K60','Waterproof'=>'10m','Battery'=>'2 batteries','Accessories'=>'Mounting kit']],

            // --- Watches ---
            ['Omega Seamaster Diver 300M Co-Axial',                   'Watches',       3800, 150,'Excellent',     ['Movement'=>'Cal. 8800 Co-Axial','Case'=>'42mm Steel','Crystal'=>'Sapphire','Box'=>'Full set']],
            ['TAG Heuer Carrera Chronograph Steel',                    'Watches',       2600, 100,'Very Good',     ['Movement'=>'Automatic','Case'=>'41mm','Strap'=>'Steel bracelet','Dial'=>'Black sunray']],
            ['Seiko Prospex Turtle SPB317 Diver',                     'Watches',       590,  25, 'Like New',      ['Movement'=>'6R35 Automatic','WR'=>'200m','Case'=>'45mm','Lume'=>'LumiBrite']],
            ['Apple Watch Ultra 2 - Alpine Loop',                     'Watches',       680,  25, 'Excellent',     ['Size'=>'49mm Titanium','Battery'=>'60hr','Band'=>'Alpine Green L','Health'=>'Advanced']],
            ['Breitling Navitimer B01 Chronograph 43',                'Watches',       5200, 200,'Collector Grade',['Movement'=>'COSC B01','Case'=>'43mm','Bracelet'=>'Navitimer','Box'=>'Full set papers']],
            ['Casio G-Shock MRG-B5000 Gold',                          'Watches',       2800, 100,'Like New',      ['Movement'=>'Solar+Bluetooth','Material'=>'Titanium','WR'=>'200m','Crystal'=>'Sapphire']],

            // --- Sneakers ---
            ['Air Jordan 1 Retro High OG - Chicago Lost and Found',   'Sneakers',      320,  15, 'Deadstock',     ['Size'=>'US 10','Style'=>'DD9335-712','Box'=>'Original tagged','Authentication'=>'Legit check verified']],
            ['Nike SB Dunk Low Pro - Grateful Dead Bears',            'Sneakers',      410,  20, 'Deadstock',     ['Size'=>'US 9.5','Box'=>'Original','Colourway'=>'Opti Yellow/Pacific Blue','Authentication'=>'Verified']],
            ['Adidas Yeezy 700 V3 Azareth',                          'Sneakers',      290,  15, 'Very Good',     ['Size'=>'US 11','Worn'=>'Twice','Box'=>'Included','Authentication'=>'StockX verified']],
            ['New Balance 2002R Protection Pack Moonbeam',            'Sneakers',      195,  10, 'Deadstock',     ['Size'=>'US 9','Colourway'=>'Moonbeam','Box'=>'Original','Year'=>'2022']],
            ['Travis Scott Air Jordan 1 Low OG - Olive',             'Sneakers',      850,  30, 'Deadstock',     ['Size'=>'US 9','Box'=>'Original special edition','Authentication'=>'GOAT verified','Extras'=>'Lace set']],
            ['Off-White Nike Air Force 1 MCA Chicago',               'Sneakers',      1200, 50, 'Like New',      ['Size'=>'US 10.5','Worn'=>'Once','Box'=>'With extras','Authentication'=>'Stadium Goods verified']],

            // --- Gaming Consoles ---
            ['Sony PlayStation 5 Disc Edition + 5 Games',            'Gaming',        430,  20, 'Excellent',     ['Storage'=>'825GB SSD','Controllers'=>'2x DualSense','Games'=>'5 AAA titles','Warranty'=>'Remaining Sony']],
            ['Xbox Series X 1TB + Game Pass Ultimate 12M',           'Gaming',        360,  15, 'Like New',      ['Storage'=>'1TB Custom NVMe','Resolution'=>'4K 120fps','Game Pass'=>'12 months','Controllers'=>'2x']],
            ['Nintendo Switch OLED Model - White',                   'Gaming',        240,  10, 'Excellent',     ['Screen'=>'7" OLED','Storage'=>'64GB','Dock'=>'Enhanced','Joy-Con'=>'Pair white']],
            ['Steam Deck OLED 1TB Limited Edition',                  'Gaming',        650,  25, 'Like New',      ['Storage'=>'1TB NVMe','Screen'=>'7.4" OLED','Battery'=>'3-12hrs','Carrying Case'=>'Included']],
            ['Valve Index VR Full Kit',                              'Gaming',        780,  30, 'Very Good',     ['Display'=>'1440x1600 per eye','Refresh'=>'120Hz','Controllers'=>'Knuckles pair','Lighthouses'=>'2x']],

            // --- Headphones ---
            ['Sony WH-1000XM5 Wireless Noise Cancelling',            'Headphones',    220,  10, 'Like New',      ['Driver'=>'30mm','ANC'=>'Adaptive','Battery'=>'30hrs','Case'=>'Soft carry case']],
            ['Bose QuietComfort Ultra Headphones - Black',           'Headphones',    250,  10, 'Excellent',     ['ANC'=>'CustomTune','Battery'=>'24hrs','Bluetooth'=>'5.3','Spatial Audio'=>'Yes']],
            ['Apple AirPods Max - Space Grey',                       'Headphones',    380,  15, 'Like New',      ['Driver'=>'40mm dynamic','ANC'=>'Active','Transparency'=>'Yes','Case'=>'Smart Case included']],
            ['Sennheiser HD 660S2 Open-Back Audiophile',             'Headphones',    330,  15, 'Like New',      ['Driver'=>'150Ω dynamic','THD'=>'<0.04%','Cable'=>'4.4mm balanced','Weight'=>'260g']],
            ['Beyerdynamic DT 1990 Pro Open Reference',              'Headphones',    390,  15, 'Excellent',     ['Driver'=>'250Ω','Frequency'=>'5-40,000Hz','Pads'=>'Both sets','Cable'=>'3m coiled']],

            // --- Tablets ---
            ['Apple iPad Pro 12.9" M2 256GB + Apple Pencil 2',      'Tablets',       780,  25, 'Like New',      ['Chip'=>'Apple M2','Storage'=>'256GB','Display'=>'Liquid Retina XDR','Pencil'=>'2nd gen included']],
            ['Samsung Galaxy Tab S9 Ultra 512GB + Keyboard',         'Tablets',       820,  30, 'Excellent',     ['Storage'=>'512GB','RAM'=>'12GB','Display'=>'14.6" Dynamic AMOLED','Keyboard'=>'Included']],
            ['Microsoft Surface Pro 10 - Sapphire',                  'Tablets',       950,  35, 'Like New',      ['CPU'=>'Intel Core Ultra 7','RAM'=>'16GB','Storage'=>'512GB','Keyboard'=>'Signature included']],
            ['Apple iPad Air 11" M2 128GB - Blue',                   'Tablets',       490,  20, 'Like New',      ['Chip'=>'Apple M2','Storage'=>'128GB','Network'=>'WiFi+Cellular','Color'=>'Blue']],

            // --- Luxury Items ---
            ['Louis Vuitton Neverfull MM - Monogram Canvas',         'Luxury',        980,  40, 'Very Good',     ['Material'=>'Monogram Canvas','Size'=>'MM 32x29x17cm','Hardware'=>'Gold-tone','Authentication'=>'Entrupy verified']],
            ['Gucci Ophidia GG Medium Tote - Beige/Ebony',          'Luxury',        750,  30, 'Excellent',     ['Material'=>'GG Supreme canvas','Lining'=>'Microfibre','Closure'=>'Magnetic snap','Year'=>'2023']],
            ['Hermès Evelyne TPM - Etain Clemence',                  'Luxury',        2800, 100,'Good',          ['Material'=>'Clemence leather','Size'=>'Small 22cm','Hardware'=>'Palladium','Stamp'=>'X (2016)']],
            ['Chanel Classic Flap Medium - Black Caviar GHW',        'Luxury',        5500, 200,'Excellent',     ['Material'=>'Caviar leather','Hardware'=>'Gold','Chain'=>'CC turn lock','Authentication'=>'REAL authentication']],
            ['Cartier Love Bracelet 18K Yellow Gold Size 17',        'Luxury',        6200, 250,'Very Good',     ['Metal'=>'18K Yellow Gold','Width'=>'6.1mm','Size'=>'17','Screwdriver'=>'Included']],

            // --- Cars & Collectibles ---
            ['1:18 Scale Lamborghini Huracán STO - Orange',          'Cars',          290,  15, 'Collector Grade',['Scale'=>'1:18','Color'=>'Arancio Borealis','Brand'=>'Burago','Box'=>'Original display box']],
            ['Formula 1 Lewis Hamilton Signed Helmet 2021',          'Collectibles',  3200, 100,'Collector Grade',['Signed by'=>'Lewis Hamilton','Year'=>'2021','COA'=>'Beckett Authentication','Display'=>'Stand included']],
            ['Vintage 1960s Omega Speedmaster Pre-Moon Parts',       'Collectibles',  4800, 200,'Collector Grade',['Reference'=>'2998/105.002','Dial'=>'Step','Lume'=>'Radium plots','Case'=>'Period correct']],
            ['LEGO Technic Bugatti Chiron 42083 - Sealed',           'Collectibles',  380,  15, 'Collector Grade',['Pieces'=>'3599','Set'=>'42083','Year'=>'2018','Condition'=>'Factory sealed']],
            ['First Edition Harry Potter Philosopher Stone 1997',    'Collectibles',  2600, 100,'Good',          ['Edition'=>'First UK printing','Publisher'=>'Bloomsbury','Printing'=>'500 copies','Condition'=>'Readable wear']],
            ['Apple Lisa Computer - 1983 Original Working',          'Collectibles',  8500, 300,'Collector Grade',['Year'=>'1983','Condition'=>'Powers on','Storage'=>'5MB HDD','Rarity'=>'Extremely rare']],
            ['Babe Ruth 1933 Goudey Baseball Card PSA 4',            'Collectibles',  3800, 150,'Collector Grade',['Card'=>'#181 1933 Goudey','Grade'=>'PSA 4 VG-EX','Player'=>'Babe Ruth','Population'=>'112 at grade']],
            ['Signed Jordan 45 Bulls Jersey Upper Deck Auth',        'Collectibles',  2100, 75, 'Collector Grade',['Player'=>'Michael Jordan','Number'=>'45','Auth'=>'Upper Deck Authenticated','Frame'=>'Display frame']],

            // --- Furniture ---
            ['Eames Lounge Chair & Ottoman - Walnut/Black',          'Furniture',     2400, 100,'Very Good',     ['Designer'=>'Eames','Material'=>'Walnut veneer & leather','Origin'=>'Herman Miller','Condition'=>'Light patina']],
            ['Fritz Hansen Series 7 Dining Chairs Set of 4',        'Furniture',     820,  30, 'Very Good',     ['Designer'=>'Arne Jacobsen','Color'=>'Black lacquer','Condition'=>'Light scratches underseat','Set'=>'4 chairs']],
            ['Le Corbusier LC4 Chaise Longue - Black Leather',      'Furniture',     1600, 50, 'Excellent',     ['Frame'=>'Chrome steel','Leather'=>'Black','Adjustable'=>'Yes','Reproduction'=>'High quality']],
            ['Vintage George Nelson Ball Clock - Vitra',             'Furniture',     310,  15, 'Good',          ['Designer'=>'George Nelson','Brand'=>'Vitra','Color'=>'Multi','Year'=>'Reissue circa 2015']],

            // --- Additional Mixed ---
            ['DJI Mavic 3 Pro Drone + RC Controller',               'Electronics',   1480, 50, 'Like New',      ['Camera'=>'Triple 4/3 CMOS','Flight Time'=>'43min','Range'=>'15km OcuSync 3.0','Included'=>'Fly more kit']],
            ['Apple MacBook Pro 16" M3 Max 64GB/2TB',              'Laptops',       2800, 100,'Like New',      ['Chip'=>'M3 Max','RAM'=>'64GB','Storage'=>'2TB','Battery Cycles'=>'12']],
            ['Sony FX3 Full-Frame Cinema Camera',                   'Cameras',       2950, 100,'Excellent',     ['Sensor'=>'12.1MP Full-Frame','ISO'=>'Up to 409600','Video'=>'4K 120fps','Body'=>'Compact cinema']],
            ['Patek Philippe Aquanaut Ref 5168 Travel Time',        'Watches',       28000,500,'Excellent',     ['Movement'=>'Cal. 324 S C FUS','Functions'=>'Dual Time Zone','Case'=>'42mm Rose Gold','Papers'=>'Included 2021']],
            ['Tesla Cybertruck 1:10 Die-Cast Collector Model',     'Cars',          185,  10, 'Collector Grade',['Scale'=>'1:10','Material'=>'Die-cast metal','Color'=>'Polished Steel','Box'=>'Display box']],
        ];

        $descriptions = [
            'Carefully inspected and photographed for this auction. The item is fully functional, includes all accessories listed in the specification panel, and ships with secure packaging after payment confirmation.',
            'A collector-grade listing from a verified seller. Expect only light signs of handling where noted, clean presentation, and a transparent bidding history throughout the auction.',
            'Premium marketplace listing with clear ownership history, realistic reserve pricing, and fast insured delivery. Ideal for buyers who want a ready-to-use item without retail markup.',
            'This lot has been independently reviewed for authenticity and condition. All details in the specification panel are accurate and verified. Ships within 48 hours of auction close.',
            'Sourced from a private collection. Items is in the stated condition with no hidden defects. Detailed photographs available on request before bidding closes.',
        ];

        // Status distribution: 40% active, 30% upcoming, 30% closed
        $total = count($items);

        foreach ($items as $idx => $item) {
            [$title, $catName, $startingPrice, $increment, $condition, $specs] = $item;

            $category = $cats[$catName];

            // Determine status bucket
            $bucket = $idx % 10;
            if ($bucket < 4) {
                // Active (40%)
                $startsAt = now()->subHours(random_int(2, 72));
                $endsAt   = now()->addHours(random_int(6, 144));
                $closed   = false;
            } elseif ($bucket < 7) {
                // Closed (30%)
                $startsAt = now()->subDays(random_int(7, 20));
                $endsAt   = now()->subHours(random_int(2, 48));
                $closed   = true;
            } else {
                // Upcoming (30%)
                $startsAt = now()->addHours(random_int(4, 96));
                $endsAt   = (clone $startsAt)->addDays(random_int(3, 10));
                $closed   = false;
            }

            $seller = $users->random();

            $auction = Auction::create([
                'user_id'         => $seller->id,
                'category_id'     => $category->id,
                'title'           => $title,
                'description'     => $descriptions[$idx % count($descriptions)]
                    . ' This lot is popular with buyers tracking '
                    . $catName . ' auctions and has been priced to encourage competitive bidding.',
                'starting_price'  => $startingPrice,
                'min_increment'   => $increment,
                'current_price'   => null,
                'image_path'      => 'auctions/' . $this->imageFor($catName),
                'gallery_images'  => $this->galleryFor($catName),
                'condition'       => $condition,
                'specifications'  => $specs,
                'shipping_details'=> random_int(0, 1)
                    ? 'Insured tracked shipping within 2–4 business days of auction close.'
                    : 'Local pickup available or insured courier dispatched within 48 hours.',
                'approval_status' => 'approved',
                'is_featured'     => $idx < 8,
                'starts_at'       => $startsAt,
                'ends_at'         => $endsAt,
            ]);

            // Create bids for active and closed auctions
            if (!$startsAt->isFuture()) {
                $amount      = (float) $startingPrice;
                $bidderPool  = $users->where('id', '!=', $seller->id)->values();
                $numBids     = $closed ? random_int(5, 14) : random_int(2, 9);
                $bidders     = $bidderPool->random(min($numBids, $bidderPool->count()));
                $lastBidderId = null;

                foreach ($bidders as $bidder) {
                    $amount += $increment + random_int(0, (int)($increment * 3));
                    Bid::create([
                        'auction_id' => $auction->id,
                        'user_id'    => $bidder->id,
                        'amount'     => round($amount, 2),
                        'created_at' => $closed
                            ? now()->subHours(random_int(48, 200))
                            : now()->subMinutes(random_int(5, 600)),
                        'updated_at' => now(),
                    ]);
                    $lastBidderId = $bidder->id;
                }

                $auction->update([
                    'current_price' => round($amount, 2),
                    'closed_at'     => $closed ? $endsAt : null,
                    'winner_id'     => $closed ? $lastBidderId : null,
                ]);
            }
        }

        $this->command->info('✅ Added ' . count($items) . ' extra auction listings successfully!');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function imageFor(string $cat): string
    {
        return match($cat) {
            'Smartphones'  => 'iphone-15-pro.jpg',
            'Laptops'      => 'macbook-pro.jpg',
            'Cameras'      => random_int(0,1) ? 'camera.jpg' : 'vintage-camera.jpg',
            'Watches'      => 'rolex-watch.jpg',
            'Sneakers'     => 'sneakers.jpg',
            'Cars'         => 'sports-car.jpg',
            'Gaming'       => 'gaming-console.jpg',
            'Luxury'       => 'luxury-bag.jpg',
            'Collectibles' => random_int(0,1) ? 'turntable.jpg' : 'vintage-camera.jpg',
            'Headphones'   => 'headphones.jpg',
            'Tablets'      => 'macbook-pro.jpg',
            'Furniture'    => 'turntable.jpg',
            default        => 'drone.jpg',
        };
    }

    private function galleryFor(string $cat): array
    {
        $main  = $this->imageFor($cat);
        $extra = ['camera.jpg', 'sneakers.jpg', 'rolex-watch.jpg', 'headphones.jpg', 'luxury-bag.jpg'];
        return [
            'auctions/' . $main,
            'auctions/' . $extra[array_rand($extra)],
            'auctions/' . $extra[array_rand($extra)],
        ];
    }
}
