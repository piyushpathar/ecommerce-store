@props(['defaultState' => '', 'defaultCity' => '', 'stateClass' => '', 'cityClass' => ''])

<div x-data="{
    statesData: {
        'Andaman & Nicobar Islands': [
            'Port Blair', 'Havelock Island (Swaraj Dweep)', 'Neil Island (Shaheed Dweep)', 'Diglipur', 
            'Mayabunder', 'Rangat', 'Car Nicobar', 'Campbell Bay', 'Garacharma', 'Bambooflat', 'Hut Bay'
        ],
        'Andhra Pradesh': [
            'Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Kakinada', 'Rajamahendravaram', 
            'Kadapa', 'Tirupati', 'Anantapur', 'Vizianagaram', 'Eluru', 'Nandyal', 'Ongole', 'Adoni', 
            'Madanapalle', 'Machilipatnam', 'Tenali', 'Proddatur', 'Chittoor', 'Hindupur', 'Bhimavaram', 
            'Guntakal', 'Dharmavaram', 'Gudivada', 'Srikakulam', 'Narasaraopet', 'Tadepalligudem', 
            'Amaravati', 'Mangalagiri', 'Tadipatri', 'Chilakaluripet', 'Anakapalle', 'Bapatla', 'Palakollu', 'Markapur'
        ],
        'Arunachal Pradesh': [
            'Itanagar', 'Naharlagun', 'Pasighat', 'Tawang', 'Ziro', 'Bomdila', 'Tezu', 'Roing', 
            'Along (Aalo)', 'Changlang', 'Khonsa', 'Namsai', 'Seppa', 'Dirang', 'Bhalukpong', 
            'Yingkiong', 'Koloriang', 'Anini', 'Hawai', 'Longding', 'Basar', 'Boleng'
        ],
        'Assam': [
            'Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon', 'Tinsukia', 'Tezpur', 'Bongaigaon', 
            'Diphu', 'Dhubri', 'North Lakhimpur', 'Karimganj', 'Sivasagar', 'Goalpara', 'Barpeta', 
            'Lumding', 'Mangaldai', 'Haflong', 'Morigaon', 'Hailakandi', 'Golaghat', 'Hojai', 
            'Dhemaji', 'Kokrajhar', 'Biswanath Chariali', 'Nalbari', 'Rangia', 'Digboi'
        ],
        'Bihar': [
            'Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Purnia', 'Darbhanga', 'Bihar Sharif', 'Arrah', 
            'Begusarai', 'Katihar', 'Munger', 'Chhapra', 'Danapur', 'Bettiah', 'Saharsa', 'Sasaram', 
            'Hajipur', 'Dehri', 'Siwan', 'Motihari', 'Nawada', 'Bagaha', 'Buxar', 'Kishanganj', 
            'Sitamarhi', 'Jamalpur', 'Jehanabad', 'Aurangabad', 'Lakhisarai', 'Madhubani', 'Samastipur', 
            'Supaul', 'Gopalganj', 'Bhabua', 'Khagaria', 'Madhepura', 'Forbesganj', 'Jamui'
        ],
        'Chandigarh': [
            'Chandigarh', 'Manimajra', 'Sector 17', 'Sector 35', 'Sector 22', 'Sector 43', 
            'Industrial Area Phase 1', 'Industrial Area Phase 2', 'IT Park'
        ],
        'Chhattisgarh': [
            'Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Rajnandgaon', 'Durg', 'Raigarh', 'Jagdalpur', 
            'Ambikapur', 'Dhamtari', 'Mahasamund', 'Chirmiri', 'Bhatapara', 'Kawardha', 'Kanker', 
            'Kondagaon', 'Balod', 'Bemetara', 'Janjgir', 'Champa', 'Surajpur', 'Mungeli', 'Gariaband', 
            'Baikunthpur', 'Manendragarh'
        ],
        'Dadra & Nagar Haveli and Daman & Diu': [
            'Daman', 'Diu', 'Silvassa', 'Naroli', 'Dadra', 'Amli', 'Bhimpore', 'Dunetha', 
            'Kadaiya', 'Nani Daman', 'Moti Daman', 'Khanvel'
        ],
        'Delhi': [
            'New Delhi', 'Central Delhi', 'North Delhi', 'South Delhi', 'East Delhi', 'West Delhi', 
            'North East Delhi', 'North West Delhi', 'South West Delhi', 'South East Delhi', 'Shahdara', 
            'Dwarka', 'Rohini', 'Connaught Place', 'Saket', 'Vasant Kunj', 'Janakpuri', 'Karol Bagh', 
            'Lajpat Nagar', 'Pitampura', 'Laxmi Nagar', 'Mayur Vihar', 'Chandni Chowk', 'Hauz Khas', 
            'Greater Kailash', 'Paschim Vihar', 'Preet Vihar', 'Okhla'
        ],
        'Goa': [
            'Panaji', 'Margao', 'Vasco da Gama', 'Mapusa', 'Ponda', 'Bicholim', 'Curchorem', 
            'Cuncolim', 'Quepem', 'Canacona', 'Pernem', 'Valpoi', 'Calangute', 'Candolim', 
            'Porvorim', 'Colva', 'Sanguem', 'Sanquelim'
        ],
        'Gujarat': [
            'Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar', 'Junagadh', 'Gandhinagar', 
            'Anand', 'Navsari', 'Bharuch', 'Morbi', 'Vapi', 'Porbandar', 'Mehsana', 'Bhuj', 'Valsad', 
            'Palanpur', 'Godhra', 'Patan', 'Kalol', 'Dahod', 'Botad', 'Amreli', 'Deesa', 'Jetpur', 
            'Gondal', 'Somnath', 'Veraval', 'Surendranagar', 'Modasa', 'Himatnagar', 'Vyara', 'Ankleshwar', 
            'Bardoli', 'Keshod', 'Mandvi', 'Gandhidham', 'Dhrangadhra', 'Khambhat', 'Petlad', 'Sanand', 
            'Wadhwan', 'Halol', 'Savarkundla', 'Una', 'Dabhoi', 'Kadi', 'Siddhpur', 'Idar', 'Anjar', 'Dwarka'
        ],
        'Haryana': [
            'Gurugram', 'Faridabad', 'Panipat', 'Ambala', 'Yamunanagar', 'Rohtak', 'Hisar', 'Karnal', 
            'Sonipat', 'Panchkula', 'Bhiwani', 'Sirsa', 'Bahadurgarh', 'Jind', 'Thanesar', 'Kaithal', 
            'Rewari', 'Palwal', 'Hansi', 'Narnaul', 'Fatehabad', 'Gohana', 'Tohana', 'Narwana', 
            'Charkhi Dadri', 'Jhajjar', 'Kurukshetra', 'Jagadhri', 'Pehowa', 'Shahbad'
        ],
        'Himachal Pradesh': [
            'Shimla', 'Dharamshala', 'Solan', 'Mandi', 'Kullu', 'Manali', 'Baddi', 'Nahan', 
            'Paonta Sahib', 'Bilaspur', 'Hamirpur', 'Una', 'Chamba', 'Palampur', 'Kangra', 
            'Nalagarh', 'Sundernagar', 'Kalka', 'Parwanoo', 'Keylong', 'Reckong Peo', 'Rampur Bushahr', 
            'Dalhousie', 'Kasauli'
        ],
        'Jammu & Kashmir': [
            'Srinagar', 'Jammu', 'Anantnag', 'Baramulla', 'Udhampur', 'Sopore', 'Kathua', 'Ganderbal', 
            'Pulwama', 'Kupwara', 'Poonch', 'Rajouri', 'Budgam', 'Bandipora', 'Kulgam', 'Reasi', 
            'Doda', 'Kishtwar', 'Samba', 'Shopian', 'Akhnoor', 'Katra', 'Pahalgam', 'Gulmarg'
        ],
        'Jharkhand': [
            'Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro Steel City', 'Deoghar', 'Hazaribagh', 'Giridih', 
            'Ramgarh', 'Medininagar (Daltonganj)', 'Chirkunda', 'Phusro', 'Chaibasa', 'Gumla', 'Dumka', 
            'Godda', 'Sahibganj', 'Simdega', 'Koderma', 'Chatra', 'Pakur', 'Jamtara', 'Latehar', 
            'Saraikela', 'Chakradharpur'
        ],
        'Karnataka': [
            'Bengaluru', 'Mysuru', 'Hubballi-Dharwad', 'Mangaluru', 'Belagavi', 'Davanagere', 'Ballari', 
            'Vijayapura', 'Shivamogga', 'Tumakuru', 'Raichur', 'Bidar', 'Udupi', 'Hosapete', 
            'Gadag-Betageri', 'Robertsonpet', 'Hassan', 'Bhadravati', 'Chitradurga', 'Kolar', 'Mandya', 
            'Chikkamagaluru', 'Gangavathi', 'Bagalkot', 'Ranebennuru', 'Karwar', 'Sirsi', 'Madikeri', 
            'Haveri', 'Yadgir', 'Chikkaballapur', 'Ramanagara', 'Dandeli', 'Gokarna', 'Chintamani'
        ],
        'Kerala': [
            'Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Kollam', 'Thrissur', 'Kannur', 'Alappuzha', 
            'Kottayam', 'Palakkad', 'Malappuram', 'Manjeri', 'Thalassery', 'Ponnani', 'Vatakara', 
            'Kanhangad', 'Payyanur', 'Koyilandy', 'Neyyattinkara', 'Kayamkulam', 'Nedumangad', 
            'Kasaragod', 'Wayanad (Kalpetta)', 'Idukki (Painavu)', 'Pathanamthitta', 'Tirur', 
            'Perinthalmanna', 'Aluva', 'Munnar', 'Varkala', 'Cherthala', 'Chalakudy', 'Kothamangalam'
        ],
        'Ladakh': [
            'Leh', 'Kargil', 'Diskit', 'Nubra', 'Dras', 'Padum', 'Zanskar', 'Nyoma', 'Khaltsi', 
            'Sankoo', 'Changthang', 'Turtuk'
        ],
        'Lakshadweep': [
            'Kavaratti', 'Agatti', 'Amini', 'Andrott', 'Kadmat', 'Kalpeni', 'Kiltan', 'Chetlat', 'Bitra', 'Minicoy'
        ],
        'Madhya Pradesh': [
            'Indore', 'Bhopal', 'Jabalpur', 'Gwalior', 'Ujjain', 'Sagar', 'Dewas', 'Satna', 'Ratlam', 
            'Rewa', 'Murwara (Katni)', 'Singrauli', 'Burhanpur', 'Khandwa', 'Bhind', 'Chhindwara', 
            'Guna', 'Shivpuri', 'Vidisha', 'Chhatarpur', 'Damoh', 'Mandsaur', 'Khargone', 'Neemuch', 
            'Pithampur', 'Hoshangabad (Narmadapuram)', 'Itarsi', 'Sehore', 'Betul', 'Seoni', 'Datia', 
            'Nagda', 'Dindori', 'Mandla', 'Tikamgarh', 'Shahdol', 'Balaghat', 'Barwani'
        ],
        'Maharashtra': [
            'Mumbai', 'Pune', 'Nagpur', 'Thane', 'Nashik', 'Kalyan-Dombivli', 'Vasai-Virar', 
            'Chhatrapati Sambhajinagar (Aurangabad)', 'Navi Mumbai', 'Solapur', 'Kolhapur', 'Sangli', 
            'Jalgaon', 'Akola', 'Latur', 'Dhule', 'Ahmednagar', 'Chandrapur', 'Parbhani', 'Ichalkaranji', 
            'Jalna', 'Ambarnath', 'Bhusawal', 'Panvel', 'Badlapur', 'Beed', 'Gondia', 'Satara', 'Barshi', 
            'Yavatmal', 'Achalpur', 'Dharashiv (Osmanabad)', 'Wardha', 'Nandurbar', 'Ratnagiri', 
            'Sindhudurg', 'Alibaug', 'Mahabaleshwar', 'Shirdi', 'Karad', 'Malegaon', 'Palghar'
        ],
        'Manipur': [
            'Imphal', 'Churachandpur', 'Thoubal', 'Kakching', 'Ukhrul', 'Senapati', 'Bishnupur', 
            'Tamenglong', 'Chandel', 'Jiribam', 'Kangpokpi', 'Moreh', 'Noney', 'Kamjong', 'Tengnoupal', 'Pherzawl'
        ],
        'Meghalaya': [
            'Shillong', 'Tura', 'Jowai', 'Nongpoh', 'Williamnagar', 'Baghmara', 'Resubelpara', 
            'Mairang', 'Cherrapunji (Sohra)', 'Khliehriat', 'Nongstoin', 'Dawki', 'Mawlynnong'
        ],
        'Mizoram': [
            'Aizawl', 'Lunglei', 'Champhai', 'Serchhip', 'Kolasib', 'Lawngtlai', 'Saiha', 
            'Mamit', 'Hnahthial', 'Khawzawl', 'Saitual'
        ],
        'Nagaland': [
            'Dimapur', 'Kohima', 'Mokokchung', 'Tuensang', 'Wokha', 'Zunheboto', 'Mon', 
            'Phek', 'Kiphire', 'Longleng', 'Peren', 'Chümoukedima', 'Niuland', 'Tseminyu'
        ],
        'Odisha': [
            'Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur', 'Puri', 'Balasore', 
            'Bhadrak', 'Baripada', 'Jharsuguda', 'Jeypore', 'Bargarh', 'Rayagada', 'Bolangir', 
            'Bhawanipatna', 'Dhenkanal', 'Barbil', 'Kendujhar', 'Angul', 'Paradip', 'Jatani', 
            'Koraput', 'Kendrapara', 'Jagatsinghpur', 'Malkangiri', 'Nabarangpur'
        ],
        'Puducherry': [
            'Puducherry', 'Karaikal', 'Mahe', 'Yanam', 'Ozhukarai', 'Villianur', 'Bahour', 
            'Ariyankuppam', 'Kalapet', 'Lawspet', 'Muthialpet'
        ],
        'Punjab': [
            'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali (SAS Nagar)', 
            'Hoshiarpur', 'Pathankot', 'Moga', 'Batala', 'Abohar', 'Malerkotla', 'Khanna', 
            'Phagwara', 'Muktsar', 'Barnala', 'Rajpura', 'Firozpur', 'Kapurthala', 'Sunam', 
            'Mansa', 'Gurdaspur', 'Fazilka', 'Faridkot', 'Nabha', 'Tarn Taran', 'Jagraon', 'Rupnagar (Ropar)'
        ],
        'Rajasthan': [
            'Jaipur', 'Jodhpur', 'Kota', 'Bikaner', 'Ajmer', 'Udaipur', 'Bhilwara', 'Alwar', 
            'Bharatpur', 'Sikar', 'Pali', 'Sri Ganganagar', 'Kishangarh', 'Baran', 'Dholpur', 
            'Sawai Madhopur', 'Churu', 'Chittorgarh', 'Beawar', 'Jhunjhunu', 'Hanumangarh', 
            'Gangapur City', 'Tonk', 'Sujangarh', 'Hindaun', 'Bhiwadi', 'Bundi', 'Nagaur', 
            'Makrana', 'Barmer', 'Jaisalmer', 'Mount Abu', 'Jalore', 'Sirohi', 'Rajsamand', 
            'Dungarpur', 'Banswara', 'Pratapgarh'
        ],
        'Sikkim': [
            'Gangtok', 'Namchi', 'Geyzing', 'Mangan', 'Rangpo', 'Singtam', 'Jorethang', 
            'Ravangla', 'Soreng', 'Pakyong', 'Nayabazar', 'Rhenock', 'Pelling', 'Yuksom'
        ],
        'Tamil Nadu': [
            'Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tiruppur', 'Erode', 
            'Tirunelveli', 'Vellore', 'Thoothukudi', 'Dindigul', 'Thanjavur', 'Ranipet', 'Sivakasi', 
            'Karur', 'Udhagamandalam (Ooty)', 'Hosur', 'Nagercoil', 'Kanchipuram', 'Kumbakonam', 
            'Cuddalore', 'Tiruvannamalai', 'Pollachi', 'Rajapalayam', 'Pudukkottai', 'Vaniyambadi', 
            'Ambur', 'Nagapattinam', 'Kanyakumari', 'Ramanathapuram', 'Karaikudi', 'Namakkal', 
            'Dharmapuri', 'Krishnagiri', 'Theni', 'Perambalur', 'Ariyalur', 'Tiruvarur', 'Mayiladuthurai', 'Chidambaram'
        ],
        'Telangana': [
            'Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Ramagundam', 'Khammam', 'Mahbubnagar', 
            'Nalgonda', 'Adilabad', 'Suryapet', 'Miryalaguda', 'Siddipet', 'Jagtial', 'Mancherial', 
            'Nirmal', 'Kamareddy', 'Kothagudem', 'Bodhan', 'Palwancha', 'Mandamarri', 'Tandur', 
            'Koratla', 'Sircilla', 'Vikarabad', 'Jangaon', 'Wanaparthy', 'Gadwal'
        ],
        'Tripura': [
            'Agartala', 'Dharmanagar', 'Udaipur', 'Kailashahar', 'Teliamura', 'Khowai', 'Belonia', 
            'Melaghar', 'Ambassa', 'Sabroom', 'Bishalgarh', 'Santirbazar', 'Kumarghat', 'Sonamura', 'Amarpur'
        ],
        'Uttar Pradesh': [
            'Lucknow', 'Kanpur', 'Ghaziabad', 'Agra', 'Meerut', 'Varanasi', 'Prayagraj', 'Bareilly', 
            'Aligarh', 'Moradabad', 'Saharanpur', 'Gorakhpur', 'Noida', 'Greater Noida', 'Firozabad', 
            'Jhansi', 'Muzaffarnagar', 'Mathura', 'Ayodhya', 'Rampur', 'Shahjahanpur', 'Farrukhabad', 
            'Maunath Bhanjan', 'Hapur', 'Etawah', 'Mirzapur', 'Bulandshahr', 'Sambhal', 'Amroha', 
            'Hardoi', 'Fatehpur', 'Raebareli', 'Orai', 'Sitapur', 'Bahraich', 'Modinagar', 'Unnao', 
            'Jaunpur', 'Lakhimpur', 'Hathras', 'Banda', 'Pilibhit', 'Barabanki', 'Basti', 'Ballia', 
            'Azamgarh', 'Sultanpur', 'Bijnor', 'Gonda', 'Deoria', 'Lalitpur', 'Kasganj', 'Mainpuri'
        ],
        'Uttarakhand': [
            'Dehradun', 'Haridwar', 'Roorkee', 'Haldwani', 'Rudrapur', 'Rishikesh', 'Kashipur', 
            'Pantnagar', 'Nainital', 'Mussoorie', 'Almora', 'Pithoragarh', 'Kotdwar', 'Ramnagar', 
            'Pauri', 'Tehri', 'Chamoli', 'Uttarkashi', 'Bageshwar', 'Champawat', 'Rudraprayag', 
            'Ranikhet', 'Vikas Nagar'
        ],
        'West Bengal': [
            'Kolkata', 'Howrah', 'Asansol', 'Siliguri', 'Durgapur', 'Bardhaman', 'Malda', 'Baharampur', 
            'Habra', 'Kharagpur', 'Shantipur', 'Dankuni', 'Dhulian', 'Ranaghat', 'Haldia', 'Raiganj', 
            'Krishnanagar', 'Nabadwip', 'Midnapore', 'Jalpaiguri', 'Balurghat', 'Basirhat', 'Bankura', 
            'Darjeeling', 'Alipurduar', 'Purulia', 'Cooch Behar', 'Kalimpong', 'Jhargram', 'Bongaon', 
            'Tamluk', 'Suri', 'Rampurhat'
        ]
    },
    selectedState: '{{ $defaultState }}',
    selectedCity: '{{ $defaultCity }}',
    stateOpen: false,
    cityOpen: false,
    stateSearch: '',
    citySearch: '',

    get stateList() {
        return Object.keys(this.statesData).sort();
    },

    get filteredStates() {
        if (!this.stateSearch.trim()) return this.stateList;
        const q = this.stateSearch.toLowerCase();
        return this.stateList.filter(s => s.toLowerCase().includes(q));
    },

    get cityList() {
        if (!this.selectedState || !this.statesData[this.selectedState]) return [];
        return this.statesData[this.selectedState];
    },

    get filteredCities() {
        if (!this.citySearch.trim()) return this.cityList;
        const q = this.citySearch.toLowerCase();
        return this.cityList.filter(c => c.toLowerCase().includes(q));
    },

    selectState(st) {
        this.selectedState = st;
        this.selectedCity = '';
        this.stateSearch = '';
        this.stateOpen = false;
        this.citySearch = '';
        this.$nextTick(() => { 
            this.cityOpen = true; 
            setTimeout(() => {
                if (this.$refs.citySearchInput) {
                    this.$refs.citySearchInput.focus();
                }
            }, 50);
        });
    },

    selectCity(ct) {
        this.selectedCity = ct;
        this.citySearch = '';
        this.cityOpen = false;
    }
}" class="contents">

    <!-- State Field Combobox -->
    <div class="relative">
        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">State / UT (All India) *</label>
        <input type="hidden" name="state" :value="selectedState" required>

        <button type="button" 
                @click="stateOpen = !stateOpen; cityOpen = false; if (stateOpen) $nextTick(() => $refs.stateSearchInput && $refs.stateSearchInput.focus())"
                class="w-full mt-0.5 px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-left text-xs flex items-center justify-between transition-all hover:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                :class="{ 'border-brand-500 ring-2 ring-brand-500/20': stateOpen }">
            <span class="truncate flex items-center gap-1.5" 
                  :class="selectedState ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-400'">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                <span x-text="selectedState || 'Select State (e.g. Gujarat)'"></span>
            </span>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" :class="{ 'rotate-180': stateOpen }"></i>
        </button>

        <!-- State Combobox Dropdown Menu with Search -->
        <div x-show="stateOpen" 
             x-cloak 
             @click.outside="stateOpen = false"
             class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-2 z-50 max-h-64 flex flex-col space-y-1.5 backdrop-blur-xl">
            
            <!-- Sticky Search Input -->
            <div class="relative px-1">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
                <input type="text" 
                       x-ref="stateSearchInput"
                       x-model="stateSearch" 
                       placeholder="Search 36 Indian States & UTs..." 
                       @click.stop
                       class="w-full pl-8 pr-2.5 py-1.5 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
            </div>

            <!-- Scrollable List -->
            <div class="overflow-y-auto no-scrollbar space-y-0.5 max-h-48 pr-0.5">
                <template x-for="st in filteredStates" :key="st">
                    <button type="button" 
                            @click="selectState(st)" 
                            class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs flex items-center justify-between transition-colors"
                            :class="selectedState === st ? 'bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                        <span class="flex items-center gap-1.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500/60"></span>
                            <span x-text="st"></span>
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-400 font-normal" x-text="statesData[st]?.length + ' cities'"></span>
                            <i x-show="selectedState === st" data-lucide="check" class="w-3 h-3 text-brand-500"></i>
                        </div>
                    </button>
                </template>
                <div x-show="filteredStates.length === 0" class="text-center py-3 text-xs text-slate-400">
                    No state matching "<span x-text="stateSearch"></span>"
                </div>
            </div>
        </div>
    </div>

    <!-- City Field Combobox -->
    <div class="relative">
        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">City / District / Town *</label>
        <input type="hidden" name="city" :value="selectedCity" required>

        <button type="button" 
                @click="if (selectedState) { cityOpen = !cityOpen; stateOpen = false; if (cityOpen) $nextTick(() => $refs.citySearchInput && $refs.citySearchInput.focus()); } else { stateOpen = true; }"
                class="w-full mt-0.5 px-3 py-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-left text-xs flex items-center justify-between transition-all hover:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                :class="{ 'border-brand-500 ring-2 ring-brand-500/20': cityOpen, 'opacity-70 cursor-pointer': !selectedState }">
            <span class="truncate flex items-center gap-1.5" 
                  :class="selectedCity ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-400'">
                <i data-lucide="building-2" class="w-3.5 h-3.5 text-brand-500 shrink-0"></i>
                <span x-text="selectedCity || (selectedState ? 'Select City (e.g. Ahmedabad)' : 'Select State First')"></span>
            </span>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" :class="{ 'rotate-180': cityOpen }"></i>
        </button>

        <!-- City Combobox Dropdown Menu with Search -->
        <div x-show="cityOpen" 
             x-cloak 
             @click.outside="cityOpen = false"
             class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-[#121824] rounded-2xl shadow-pop border border-slate-200 dark:border-white/10 p-2 z-50 max-h-64 flex flex-col space-y-1.5 backdrop-blur-xl">
            
            <!-- Sticky Search Input -->
            <div class="relative px-1">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
                <input type="text" 
                       x-ref="citySearchInput"
                       x-model="citySearch" 
                       :placeholder="selectedState ? 'Search ' + selectedState + ' cities...' : 'Search cities...'" 
                       @click.stop
                       class="w-full pl-8 pr-2.5 py-1.5 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
            </div>

            <!-- Scrollable List -->
            <div class="overflow-y-auto no-scrollbar space-y-0.5 max-h-48 pr-0.5">
                <template x-for="ct in filteredCities" :key="ct">
                    <button type="button" 
                            @click="selectCity(ct)" 
                            class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs flex items-center justify-between transition-colors"
                            :class="selectedCity === ct ? 'bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5'">
                        <span x-text="ct" class="truncate"></span>
                        <i x-show="selectedCity === ct" data-lucide="check" class="w-3 h-3 text-brand-500"></i>
                    </button>
                </template>
                
                <!-- Custom City Entry If Not In Predefined List -->
                <div x-show="citySearch.trim().length > 1 && !cityList.map(c => c.toLowerCase()).includes(citySearch.trim().toLowerCase())" class="pt-1.5 border-t border-slate-100 dark:border-white/5">
                    <button type="button" 
                            @click="selectCity(citySearch.trim())" 
                            class="w-full text-left px-2.5 py-2 rounded-lg text-xs bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 font-bold hover:underline flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-500"></i>
                        <span>Use custom city: "<span x-text="citySearch.trim()"></span>"</span>
                    </button>
                </div>

                <div x-show="filteredCities.length === 0 && (!citySearch.trim() || citySearch.trim().length <= 1)" class="text-center py-3 text-xs text-slate-400">
                    <span x-text="selectedState ? 'Type to search or enter custom city' : 'Please select a state first'"></span>
                </div>
            </div>
        </div>
    </div>
</div>
