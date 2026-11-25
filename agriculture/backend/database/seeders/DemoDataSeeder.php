<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** Extra realistic demo data. Idempotent (upsert/updateOrInsert). Login password for all demo users: password123 */
class DemoDataSeeder extends Seeder
{
    private function up(string $table, array $rows, array $keys): void
    {
        $all = [];
        foreach ($rows as $r) { $all = array_unique([...$all, ...array_keys($r)]); }
        $rows = array_map(fn ($r) => array_replace(array_fill_keys($all, null), $r), $rows);
        DB::table($table)->upsert($rows, $keys);
    }

    public function run(): void
    {
        $now = now();
        $pw = Hash::make('password123');
        $j = fn ($a) => json_encode($a, JSON_UNESCAPED_UNICODE);
        $img = [
            'rice' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&q=80',
            'veg' => 'https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=400&q=80',
            'potato' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=400&q=80',
            'mango' => 'https://images.unsplash.com/photo-1519096845289-95806ee03a1a?w=400&q=80',
            'tool' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&q=80',
            'spray' => 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=400&q=80',
        ];

        // ---- Users per role ----
        $farmers = [
            ['Rafiqul Islam', 'রফিকুল ইসলাম', 'Rangpur', 'Rangpur', 'Mithapukur', 'Balarhat', 6.5, ['Rice (Boro)', 'Potato', 'Maize']],
            ['Shirin Akter', 'শিরিন আক্তার', 'Khulna', 'Jashore', 'Keshabpur', 'Sufalakati', 3.0, ['Rice (Aman)', 'Tomato', 'Brinjal']],
            ['Jahangir Alam', 'জাহাঙ্গীর আলম', 'Chattogram', 'Cumilla', 'Debidwar', 'Fatehpur', 5.0, ['Rice (Aman)', 'Chilli', 'Onion']],
            ['Nurul Haque', 'নূরুল হক', 'Sylhet', 'Sylhet', 'Jaintiapur', 'Nijpat', 2.5, ['Tea', 'Lemon', 'Rice (Boro)']],
            ['Salma Khatun', 'সালমা খাতুন', 'Rajshahi', 'Natore', 'Singra', 'Chamari', 4.0, ['Mango', 'Wheat', 'Mustard']],
            ['Monir Hossain', 'মনির হোসেন', 'Dhaka', 'Manikganj', 'Singair', 'Baliati', 3.5, ['Jute', 'Rice (Aman)', 'Cabbage']],
            ['Ayesha Siddika', 'আয়েশা সিদ্দিকা', 'Barishal', 'Patuakhali', 'Kalapara', 'Mohipur', 2.0, ['Coconut', 'Betel Leaf', 'Rice (Aman)']],
            ['Kamal Uddin', 'কামাল উদ্দিন', 'Mymensingh', 'Netrokona', 'Kendua', 'Asma', 8.0, ['Rice (Boro)', 'Mustard', 'Garlic']],
            ['Rokeya Begum', 'রোকেয়া বেগম', 'Rangpur', 'Dinajpur', 'Birampur', 'Kalikapur', 3.2, ['Potato', 'Maize', 'Lychee']],
            ['Abul Kalam', 'আবুল কালাম', 'Khulna', 'Satkhira', 'Shyamnagar', 'Gabura', 1.8, ['Shrimp Feed Crop', 'Rice (Aman)', 'Watermelon']],
            ['Taslima Nasrin', 'তাসলিমা নাসরিন', 'Rajshahi', 'Chapainawabganj', 'Shibganj', 'Kansat', 5.5, ['Mango', 'Banana', 'Wheat']],
            ['Harun Or Rashid', 'হারুন অর রশিদ', 'Dhaka', 'Gazipur', 'Kaliakair', 'Fulbaria', 2.8, ['Tomato', 'Cauliflower', 'Rice (Boro)']],
        ];
        foreach ($farmers as $i => [$en, $bn, $div, $dist, $upz, $vil, $land, $crops]) {
            $n = $i + 4; $id = sprintf('usr_%03d', $n);
            DB::table('users')->updateOrInsert(['public_id' => $id], [
                'tenant_id' => $n % 2 ? 'tenant_001' : 'tenant_002', 'role' => 'farmer', 'name' => $en, 'name_bn' => $bn,
                'email' => 'farmer' . $n . '@farmer.com', 'phone' => '0171' . sprintf('%07d', 3000100 + $n),
                'avatar' => 'https://i.pravatar.cc/150?img=' . (30 + $n), 'division' => $div, 'district' => $dist, 'upazila' => $upz,
                'access_label' => 'Registered Farmer', 'password' => $pw, 'created_at' => $now->copy()->subDays(80 - $n * 3), 'updated_at' => $now,
            ]);
            $this->up('farmers', [[
                'id' => $id, 'user_id' => $id, 'tenant_id' => $n % 2 ? 'tenant_001' : 'tenant_002', 'fid' => sprintf('AGS-2026-%07d', $n),
                'nid_hash' => 'hash_demo_' . $n, 'name_bn' => $bn, 'name_en' => $en, 'division' => $div, 'district' => $dist, 'upazila' => $upz,
                'village' => $vil, 'land_size' => $land, 'crop_types' => $j($crops), 'phone' => '0171' . sprintf('%07d', 3000100 + $n),
                'bkash_account' => '0171' . sprintf('%07d', 3000100 + $n), 'verified' => $n % 5 !== 0, 'blocked' => false,
                'created_at' => $now->copy()->subDays(80 - $n * 3), 'updated_at' => $now,
            ]], ['id']);
        }
        $fids = array_map(fn ($i) => sprintf('usr_%03d', $i + 4), array_keys($farmers));
        $allFarmers = array_merge(['usr_001', 'usr_002', 'usr_003'], $fids);

        $officers = [
            ['usr_off_003', 'Dr. Mizanur Rahman', 'ডঃ মিজানুর রহমান', 'Rangpur', ['Potato Disease', 'Maize'], ['Rangpur', 'Dinajpur', 'Gaibandha']],
            ['usr_off_004', 'Farhana Yasmin', 'ফারহানা ইয়াসমিন', 'Khulna', ['Rice Disease', 'Saline Soil'], ['Khulna', 'Jashore', 'Satkhira']],
            ['usr_off_005', 'Abdus Salam', 'আবদুস সালাম', 'Sylhet', ['Tea', 'Citrus'], ['Sylhet', 'Moulvibazar']],
            ['usr_off_006', 'Dr. Shamima Nasrin', 'ডঃ শামীমা নাসরিন', 'Dhaka', ['Vegetable', 'Fruit Orchard'], ['Dhaka', 'Gazipur', 'Manikganj']],
        ];
        foreach ($officers as $k => [$id, $en, $bn, $div, $tags, $regions]) {
            DB::table('users')->updateOrInsert(['public_id' => $id], [
                'tenant_id' => 'tenant_001', 'role' => 'officer', 'name' => $en, 'name_bn' => $bn, 'email' => 'officer' . ($k + 3) . '@dae.gov.bd',
                'phone' => '0181100' . sprintf('%04d', 30 + $k), 'avatar' => 'https://i.pravatar.cc/150?img=' . (40 + $k), 'division' => $div,
                'district' => $regions[0], 'upazila' => '', 'designation' => 'Agricultural Officer', 'access_label' => 'Regional Advisory Officer',
                'password' => $pw, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $this->up('officers', [[
                'id' => $id, 'user_id' => $id, 'tenant_id' => 'tenant_001', 'officer_id' => 'OFF-00' . ($k + 3), 'specialty_tags' => $j($tags),
                'region_districts' => $j($regions), 'max_active_cases' => 20, 'availability_status' => ['available', 'busy', 'available', 'available'][$k],
                'active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]], ['id']);
        }
        $offIds = ['usr_off_001', 'usr_off_002', 'usr_off_003', 'usr_off_004', 'usr_off_005', 'usr_off_006'];

        $vendors = [
            ['usr_vnd_003', 'vnd_003', 'Krishi Bazar Ltd.', 'কৃষি বাজার লিমিটেড', 'vendor3@ams.com.bd', ['Dhaka', 'Gazipur', 'Manikganj']],
            ['usr_vnd_004', 'vnd_004', 'GreenHarvest Agro', 'গ্রিনহারভেস্ট এগ্রো', 'vendor4@ams.com.bd', ['Rangpur', 'Dinajpur', 'Bogura']],
            ['usr_vnd_005', 'vnd_005', 'Padma Irrigation Tools', 'পদ্মা ইরিগেশন টুলস', 'vendor5@ams.com.bd', ['All Bangladesh']],
        ];
        foreach ($vendors as $k => [$uid, $vid, $co, $bn, $email, $dd]) {
            DB::table('users')->updateOrInsert(['public_id' => $uid], [
                'tenant_id' => 'tenant_001', 'role' => 'vendor', 'name' => $co, 'name_bn' => $bn, 'email' => $email, 'phone' => '0171100' . sprintf('%04d', 40 + $k),
                'avatar' => 'https://i.pravatar.cc/150?img=' . (50 + $k), 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => '',
                'designation' => 'Verified Vendor', 'access_label' => 'Marketplace Vendor', 'password' => $pw, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $this->up('vendors', [[
                'id' => $vid, 'user_id' => $uid, 'tenant_id' => 'tenant_001', 'vendor_id' => strtoupper(str_replace('vnd_', 'VND-', $vid)), 'company_name' => $co,
                'delivery_districts' => $j($dd), 'status' => 'approved', 'created_at' => $now, 'updated_at' => $now,
            ]], ['id']);
        }

        $companies = [
            ['usr_cmp_002', 'AgriExport Bangladesh', 'এগ্রিএক্সপোর্ট বাংলাদেশ', 'company2@ams.com.bd', ['Mango', 'Potato', 'Lychee']],
            ['usr_cmp_003', 'Meghna Foods & Agro', 'মেঘনা ফুডস অ্যান্ড এগ্রো', 'company3@ams.com.bd', ['Rice (Aman)', 'Rice (Boro)', 'Wheat', 'Mustard']],
        ];
        foreach ($companies as $k => [$uid, $co, $bn, $email, $interests]) {
            DB::table('users')->updateOrInsert(['public_id' => $uid], [
                'tenant_id' => 'tenant_001', 'role' => 'company', 'name' => $co, 'name_bn' => $bn, 'email' => $email, 'phone' => '0171100' . sprintf('%04d', 60 + $k),
                'avatar' => 'https://i.pravatar.cc/150?img=' . (60 + $k), 'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila' => '',
                'designation' => 'Procurement Company', 'access_label' => 'Crop Buyer', 'password' => $pw, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $this->up('companies', [[
                'id' => $uid, 'user_id' => $uid, 'tenant_id' => 'tenant_001', 'company_id' => 'CMP-00' . ($k + 2), 'company_name' => $co,
                'registration_no' => 'TRADE-2026-00' . ($k + 2), 'crop_interests' => $j($interests), 'created_at' => $now, 'updated_at' => $now,
            ]], ['id']);
        }

        // ---- Products (marketplace) ----
        $cats = [
            ['Urea Fertilizer 25kg', 'ইউরিয়া সার ২৫ কেজি', 'Fertilizer', 650, '25kg bag', 'KAFCO', 'tool'],
            ['TSP Fertilizer', 'টিএসপি সার', 'Fertilizer', 1100, '50kg bag', 'Chittagong Urea', 'tool'],
            ['MOP (Potash) Fertilizer', 'এমওপি (পটাশ) সার', 'Fertilizer', 980, '50kg bag', 'BCIC', 'tool'],
            ['Organic Compost', 'জৈব কম্পোস্ট সার', 'Fertilizer', 350, '20kg bag', 'GreenHarvest', 'veg'],
            ['Neem Oil Bio-Pesticide', 'নিম তেল জৈব কীটনাশক', 'Pesticide', 380, '500ml bottle', 'Agro Neem BD', 'spray'],
            ['Mancozeb Fungicide', 'ম্যানকোজেব ছত্রাকনাশক', 'Pesticide', 520, '1kg pack', 'Syngenta', 'spray'],
            ['Imidacloprid Insecticide', 'ইমিডাক্লোপ্রিড কীটনাশক', 'Pesticide', 290, '100ml bottle', 'ACI Crop Care', 'spray'],
            ['BRRI Dhan-29 Seed', 'ব্রি ধান-২৯ বীজ', 'Seed', 900, '5kg pack', 'BRRI', 'rice'],
            ['Hybrid Tomato Seed', 'হাইব্রিড টমেটো বীজ', 'Seed', 450, '10g pack', 'Lal Teer Seed', 'veg'],
            ['Certified Potato Seed (Diamant)', 'সার্টিফাইড আলু বীজ (ডায়মন্ট)', 'Seed', 3200, '50kg bag', 'BADC', 'potato'],
            ['Hybrid Maize Seed', 'হাইব্রিড ভুট্টা বীজ', 'Seed', 1400, '5kg pack', 'Pioneer', 'rice'],
            ['Power Tiller Blade Set', 'পাওয়ার টিলার ব্লেড সেট', 'Equipment', 4200, 'set', 'Metal Agro', 'tool'],
            ['Hand Sprayer 16L', 'হ্যান্ড স্প্রেয়ার ১৬ লিটার', 'Equipment', 1850, 'piece', 'Padma Tools', 'tool'],
            ['Drip Irrigation Kit (1 bigha)', 'ড্রিপ সেচ কিট (১ বিঘা)', 'Equipment', 12500, 'kit', 'Padma Irrigation', 'tool'],
            ['Solar Water Pump 1HP', 'সোলার পানির পাম্প ১ এইচপি', 'Equipment', 38000, 'piece', 'SunAgro', 'tool'],
            ['Pheromone Fruit Fly Trap', 'ফেরোমন ফাঁদ (ফলের মাছি)', 'Pesticide', 120, 'piece', 'Krishi Bazar', 'mango'],
        ];
        $vids = ['vnd_001', 'vnd_002', 'vnd_003', 'vnd_004', 'vnd_005'];
        $prods = [];
        foreach ($cats as $k => [$en, $bn, $cat, $price, $unit, $mfr, $im]) {
            $vid = $cat === 'Equipment' ? ($k % 2 ? 'vnd_005' : 'vnd_003') : $vids[$k % 4];
            $prods[] = [
                'id' => sprintf('prod_%03d', $k + 4), 'tenant_id' => 'tenant_001', 'vendor_id' => $vid, 'name_en' => $en, 'name_bn' => $bn, 'category' => $cat,
                'price' => $price, 'unit' => $unit, 'stock_qty' => 40 + ($k * 37) % 400, 'description' => $en . ' - quality assured, delivered across Bangladesh.',
                'manufacturer' => $mfr, 'photos' => $j([$img[$im]]), 'delivery_districts' => $j(['Dhaka', 'Mymensingh', 'Rajshahi', 'Rangpur', 'Khulna']),
                'estimated_delivery_days' => 2 + $k % 4, 'rating' => 4.2 + ($k % 8) / 10, 'review_count' => 20 + $k * 17, 'is_recommended' => $k % 3 === 0,
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        $this->up('products', $prods, ['id']);

        // ---- Orders (all statuses) ----
        $statuses = ['pending', 'confirmed', 'accepted', 'dispatched', 'delivered', 'completed', 'cancelled', 'delivered', 'completed', 'pending', 'dispatched', 'delivered'];
        $gws = ['bkash', 'nagad', 'cod', 'stripe'];
        $orders = []; $items = [];
        foreach ($statuses as $k => $st) {
            $oid = sprintf('ORD-2026-%08d', $k + 2);
            $farmer = $allFarmers[$k % count($allFarmers)];
            $p1 = $prods[$k % count($prods)]; $p2 = $prods[($k + 5) % count($prods)];
            $q1 = 1 + $k % 3; $q2 = 1 + ($k + 1) % 2;
            $total = $p1['price'] * $q1 + $p2['price'] * $q2;
            $placed = $now->copy()->subDays(30 - $k * 2)->setTime(10, 15);
            $done = in_array($st, ['delivered', 'completed']);
            $orders[] = [
                'id' => $oid, 'tenant_id' => 'tenant_001', 'farmer_id' => $farmer, 'vendor_id' => $p1['vendor_id'], 'status' => $st, 'total_amount' => $total,
                'payment_gateway' => $gws[$k % 4], 'payment_status' => $st === 'cancelled' ? 'refunded' : ($st === 'pending' ? 'pending' : 'paid'),
                'settlement_status' => $st === 'completed' ? 'released' : ($done ? 'ready_for_release' : 'held'),
                'estimated_delivery' => $placed->copy()->addDays(4)->toDateString(), 'delivered_at' => $done ? $placed->copy()->addDays(3) : null,
                'placed_at' => $placed, 'created_at' => $placed, 'updated_at' => $now,
            ];
            $items[] = ['order_id' => $oid, 'product_id' => $p1['id'], 'product_name' => $p1['name_en'], 'quantity' => $q1, 'price' => $p1['price'], 'unit' => $p1['unit'], 'created_at' => $now, 'updated_at' => $now];
            $items[] = ['order_id' => $oid, 'product_id' => $p2['id'], 'product_name' => $p2['name_en'], 'quantity' => $q2, 'price' => $p2['price'], 'unit' => $p2['unit'], 'created_at' => $now, 'updated_at' => $now];
        }
        $this->up('orders', $orders, ['id']);
        $this->up('order_items', $items, ['order_id', 'product_id']);

        // ---- Advisory cases ----
        $cases = [
            ['Rice (Boro)', 'Leaves have diamond-shaped grey spots with brown borders. Spreading after recent rain.', 'Rice Blast', 90, 'responded', 'Spray Tricyclazole 75WP at 0.6g/L. Avoid excess urea and drain stagnant water.'],
            ['Potato', 'Dark water-soaked patches on leaves, white fuzz underneath in the morning.', 'Late Blight', 95, 'assigned', null],
            ['Tomato', 'Leaves curling upward and turning yellow, plants stunted. Whiteflies visible.', 'Tomato Leaf Curl Virus', 88, 'ai_analyzed', null],
            ['Rice (Aman)', 'Whole panicles turning white and empty, stems have small holes near base.', 'Stem Borer', 84, 'responded', 'Install pheromone traps and apply Cartap Hydrochloride granules. Remove white-ear tillers.'],
            ['Mango', 'Black spots on flowers and young fruits, flowers dropping early.', 'Mango Anthracnose', 91, 'closed', 'Spray Carbendazim before flowering and again 15 days later. Prune crowded branches.'],
            ['Wheat', 'Brown oval spots on leaves, lower leaves drying up first.', 'Wheat Blast / Leaf Spot', 79, 'escalated', null],
            ['Maize', 'Ragged holes in whorl leaves, sawdust-like frass inside the whorl.', 'Fall Armyworm', 93, 'responded', 'Apply Emamectin Benzoate in the whorl in the evening. Scout fields twice weekly.'],
            ['Brinjal', 'Wilting of shoots and boreholes in fruits.', 'Brinjal Shoot & Fruit Borer', 86, 'assigned', null],
            ['Chilli', 'Leaves curled and puckered, tiny insects on underside.', 'Thrips / Mites', 82, 'pending', null],
            ['Mustard', 'Yellowing of lower leaves, white powdery coating on surface.', 'Powdery Mildew', 89, 'ai_analyzed', null],
        ];
        $cr = [];
        foreach ($cases as $k => [$crop, $desc, $dx, $conf, $st, $resp]) {
            $fid = $allFarmers[($k * 2 + 1) % count($allFarmers)];
            $created = $now->copy()->subDays(20 - $k * 2);
            $cr[] = [
                'id' => sprintf('ADV-2026-%07d', $k + 4), 'tenant_id' => 'tenant_001', 'farmer_id' => $fid,
                'officer_id' => in_array($st, ['pending', 'ai_analyzed']) ? null : $offIds[$k % 6], 'crop_type' => $crop, 'description' => $desc,
                'photos' => $j([array_values($img)[$k % 4]]), 'status' => $st, 'ai_diagnosis' => $st === 'pending' ? null : $dx,
                'ai_confidence' => $st === 'pending' ? null : $conf, 'officer_response' => $resp, 'responded_at' => $resp ? $created->copy()->addHours(5) : null,
                'resolved_at' => $st === 'closed' ? $created->copy()->addDays(2) : null, 'created_at' => $created, 'updated_at' => $now,
            ];
        }
        $this->up('advisory_cases', $cr, ['id']);

        // ---- Crop listings & deals ----
        $crops = [['Rice (Boro)', 'BRRI Dhan-29', 8000, 30, 'Rangpur'], ['Potato', 'Diamant', 12000, 22, 'Dinajpur'], ['Mango', 'Fazli', 3000, 85, 'Chapainawabganj'],
            ['Mustard', 'Tori-7', 2500, 95, 'Netrokona'], ['Tomato', 'BARI Tomato-14', 4000, 40, 'Gazipur'], ['Wheat', 'BARI Gom-30', 6000, 42, 'Natore'],
            ['Onion', 'Faridpuri', 5000, 55, 'Cumilla'], ['Maize', 'Hybrid NK-40', 9000, 27, 'Rangpur'], ['Garlic', 'Local', 1500, 130, 'Netrokona'], ['Jute', 'O-9897', 7000, 60, 'Manikganj']];
        $lst = []; $deals = []; $cmp = ['usr_cmp_001', 'usr_cmp_002', 'usr_cmp_003'];
        foreach ($crops as $k => [$crop, $var, $qty, $price, $dist]) {
            $id = sprintf('CRP-%03d', $k + 3); $fid = $allFarmers[($k + 3) % count($allFarmers)];
            $matched = $k % 3 === 0;
            $lst[] = [
                'id' => $id, 'tenant_id' => 'tenant_001', 'farmer_id' => $fid, 'product_category' => in_array($crop, ['Tomato', 'Onion', 'Potato']) ? 'Fresh Vegetables' : 'Grains & Cereals',
                'crop_type' => $crop, 'variety' => $var, 'quantity_kg' => $qty, 'quality_grade' => ['A', 'A', 'B'][$k % 3], 'asking_price' => $price,
                'harvest_date' => $now->copy()->addDays(10 + $k * 6)->toDateString(), 'district' => $dist, 'upazila' => 'Sadar', 'photos' => $j([array_values($img)[$k % 4]]),
                'status' => $matched ? 'matched' : 'active', 'matched_company_id' => $matched ? $cmp[$k % 3] : null, 'created_at' => $now->copy()->subDays(12 - $k), 'updated_at' => $now,
            ];
            if ($matched) {
                $agreed = $price - 2;
                $deals[] = [
                    'id' => sprintf('deal_%03d', $k + 2), 'listing_id' => $id, 'company_id' => $cmp[$k % 3], 'farmer_id' => $fid, 'agreed_price' => $agreed, 'quantity_kg' => $qty,
                    'commission_pct' => 3, 'commission_amt' => round($agreed * $qty * 0.03, 2), 'status' => $k % 2 ? 'confirmed' : 'pending',
                    'payment_gateway' => $k % 2 ? 'stripe' : null, 'payment_status' => $k % 2 ? 'paid' : 'pending', 'confirmed_at' => $k % 2 ? $now->copy()->subDays(3) : null,
                    'created_at' => $now->copy()->subDays(5), 'updated_at' => $now,
                ];
            }
        }
        $this->up('crop_listings', $lst, ['id']);
        $this->up('crop_deals', $deals, ['id']);

        // ---- Market prices (14-day history) ----
        $pr = [['Rice (Boro)', 30], ['Wheat', 42], ['Maize', 27], ['Onion', 55], ['Garlic', 130], ['Tomato', 40], ['Brinjal', 45], ['Mustard', 95], ['Jute', 60], ['Lychee', 180], ['Banana', 70], ['Chilli', 160]];
        $rows = [];
        foreach ($pr as $k => [$crop, $base]) {
            $hist = []; $p = $base * 0.92;
            for ($d = 13; $d >= 0; $d--) { $p += (($k + $d) % 5 - 1.8) * $base * 0.008 + $base * 0.004; $hist[] = ['date' => $now->copy()->subDays($d)->toDateString(), 'price' => round($p, 1)]; }
            $cur = end($hist)['price']; $prev = $hist[7]['price']; $chg = round($cur - $prev, 1); $pct = round($chg / $prev * 100, 1);
            $rows[] = ['crop_type' => $crop, 'current_price' => $cur, 'unit' => 'kg', 'change_7d' => $chg, 'change_percent' => $pct, 'trend' => $chg > 0.3 ? 'up' : ($chg < -0.3 ? 'down' : 'stable'), 'history' => $j($hist), 'created_at' => $now, 'updated_at' => $now];
        }
        $this->up('crop_prices', $rows, ['crop_type']);

        // ---- Weather (7 days, 8 districts) ----
        $conds = [['Clear Sky', 'sun'], ['Partly Cloudy', 'cloud-sun'], ['Light Rain', 'cloud-rain'], ['Thunderstorm', 'cloud-lightning'], ['Humid & Hazy', 'cloud']];
        $w = [];
        foreach (['Dhaka', 'Mymensingh', 'Rajshahi', 'Rangpur', 'Khulna', 'Sylhet', 'Chattogram', 'Barishal'] as $di => $dist) {
            for ($d = 0; $d < 7; $d++) {
                [$c, $ic] = $conds[($di + $d) % 5]; $rain = in_array($ic, ['cloud-rain', 'cloud-lightning']) ? 8 + ($di * 7 + $d * 5) % 40 : 0;
                $w[] = ['district' => $dist, 'forecast_date' => $now->copy()->addDays($d)->toDateString(), 'temp_min' => 16 + ($di + $d) % 5, 'temp_max' => 26 + ($di * 2 + $d) % 7,
                    'rainfall' => $rain, 'humidity' => 55 + ($di * 3 + $d * 4) % 35, 'wind_speed' => 6 + ($di + $d * 2) % 14, 'condition' => $c, 'icon' => $ic,
                    'advisory' => $rain > 20 ? 'Heavy rain expected: delay spraying and ensure field drainage.' : 'Good conditions for field work and irrigation scheduling.', 'created_at' => $now, 'updated_at' => $now];
            }
        }
        $this->up('weather_forecasts', $w, ['district', 'forecast_date']);

        // ---- Notifications & audit logs ----
        $n = [];
        foreach ($allFarmers as $k => $fid) {
            $n[] = ['id' => 'notif_d' . $k . 'a', 'user_id' => $fid, 'type' => 'advisory', 'title' => 'Advisory Update', 'message' => 'An officer has reviewed your advisory case.', 'channel' => $j(['push', 'sms']), 'is_read' => $k % 3 === 0, 'created_at' => $now->copy()->subHours($k * 5 + 2), 'updated_at' => $now];
            $n[] = ['id' => 'notif_d' . $k . 'b', 'user_id' => $fid, 'type' => 'price', 'title' => 'Market Price Alert', 'message' => 'Rice and potato prices changed in your district market.', 'channel' => $j(['push']), 'is_read' => false, 'created_at' => $now->copy()->subHours($k * 7 + 6), 'updated_at' => $now];
        }
        foreach (['usr_vnd_001', 'usr_vnd_002', 'usr_vnd_003'] as $k => $u) {
            $n[] = ['id' => 'notif_dv' . $k, 'user_id' => $u, 'type' => 'order', 'title' => 'New Order Received', 'message' => 'A farmer placed a new order for your product.', 'channel' => $j(['push', 'email']), 'is_read' => false, 'created_at' => $now->copy()->subHours($k + 1), 'updated_at' => $now];
        }
        foreach ($offIds as $k => $u) {
            $n[] = ['id' => 'notif_do' . $k, 'user_id' => $u, 'type' => 'advisory', 'title' => 'New Case Assigned', 'message' => 'A new advisory case was assigned to you.', 'channel' => $j(['push', 'email']), 'is_read' => $k % 2 === 0, 'created_at' => $now->copy()->subHours($k * 3 + 1), 'updated_at' => $now];
        }
        $this->up('notifications', $n, ['id']);

        $a = [];
        foreach ([['tenant', 'update', 'Upgraded North Bengal Agro Network to standard plan.'], ['order', 'settlement', 'Released settlement for ORD-2026-00000006.'], ['farmer', 'verify', 'Verified farmer AGS-2026-0000005.'],
            ['officer', 'create', 'Onboarded officer Farhana Yasmin.'], ['vendor', 'approve', 'Approved vendor Krishi Bazar Ltd.'], ['case', 'escalate', 'Escalated wheat case ADV-2026-0000009 to senior officer.'],
            ['farmer', 'block', 'Blocked duplicate farmer account after NID check.'], ['deal', 'confirm', 'Confirmed mango deal between farmer and AgriExport Bangladesh.']] as $k => [$e, $ac, $d]) {
            $a[] = ['id' => 'audit_d' . ($k + 1), 'entity' => $e, 'action' => $ac, 'actor' => 'Super Admin', 'details' => $d, 'created_at' => $now->copy()->subDays(8 - $k), 'updated_at' => $now];
        }
        $this->up('audit_logs', $a, ['id']);
    }
}
