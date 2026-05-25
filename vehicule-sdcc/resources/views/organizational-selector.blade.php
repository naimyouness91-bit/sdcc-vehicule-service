<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélecteur d'Organisation SDCC</title>
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/all.min.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f1f8f4 0%, #fff3e0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 50px;
            max-width: 500px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .form-label .required {
            color: #ff4444;
        }

        .input-wrapper {
            position: relative;
        }

        .org-input {
            width: 100%;
            padding: 16px 16px 16px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .org-input::placeholder {
            color: #999;
        }

        .org-input:focus {
            outline: none;
            border-color: #4CAF50;
            background: white;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(90deg, #4CAF50 0%, #FFA726 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 18px;
            pointer-events: none;
        }

        .clear-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            font-size: 18px;
            padding: 0;
            display: none;
            transition: all 0.2s;
        }

        .clear-btn:hover {
            color: #ff4444;
        }

        .clear-btn.show {
            display: flex;
        }

        .dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dropdown.show {
            display: block;
        }

        .dropdown-item {
            padding: 14px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: #f9f9f9;
        }

        .dropdown-item.active {
            background: linear-gradient(90deg, rgba(76, 175, 80, 0.1) 0%, rgba(255, 167, 38, 0.1) 100%);
            color: #4CAF50;
            font-weight: 500;
        }

        .item-hierarchy {
            font-size: 13px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .item-hierarchy .breadcrumb {
            color: #999;
            font-size: 12px;
        }

        .no-results {
            padding: 20px;
            text-align: center;
            color: #999;
            font-size: 14px;
        }

        .selected-value {
            padding: 16px;
            background: linear-gradient(90deg, rgba(76, 175, 80, 0.05) 0%, rgba(255, 167, 38, 0.05) 100%);
            border-radius: 10px;
            border-left: 4px solid #4CAF50;
            margin-top: 20px;
            display: none;
        }

        .selected-value.show {
            display: block;
        }

        .selected-value-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .selected-value-content {
            font-size: 16px;
            color: #4CAF50;
            font-weight: 500;
            word-break: break-word;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 32px;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #FFA500 0%, #FFA726 25%, #66BB6A 75%, #4CAF50 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 165, 0, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Scrollbar styling */
        .dropdown::-webkit-scrollbar {
            width: 8px;
        }

        .dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .dropdown::-webkit-scrollbar-thumb {
            background: #4CAF50;
            border-radius: 10px;
        }

        .dropdown::-webkit-scrollbar-thumb:hover {
            background: #FFA726;
        }

        @media (max-width: 600px) {
            .container {
                padding: 40px 20px;
            }

            .title {
                font-size: 24px;
            }

            .dropdown {
                max-height: 300px;
            }

            .logo {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <i class="fas fa-sitemap"></i>
            </div>
            <h1 class="title">Sélectionner l'Organisation</h1>
            <p class="subtitle">Choisissez votre direction, département et service</p>
        </div>

        <!-- Form -->
        <form id="orgForm">
            <div class="form-group">
                <label class="form-label">
                    Organisation <span class="required">*</span>
                </label>
                <div class="input-wrapper">
                    <i class="fas fa-building input-icon"></i>
                    <input
                        type="text"
                        id="orgInput"
                        class="org-input"
                        placeholder="Chercher ou sélectionner..."
                        autocomplete="off"
                        required
                    >
                    <button type="button" class="clear-btn" id="clearBtn" title="Effacer">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="dropdown" id="dropdown"></div>
                </div>
            </div>

            <!-- Selected Value Display -->
            <div class="selected-value" id="selectedValue">
                <div class="selected-value-title">Sélectionné</div>
                <div class="selected-value-content" id="selectedContent"></div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn" disabled>
                <i class="fas fa-check"></i> Confirmer la sélection
            </button>
        </form>
    </div>

    <script>
        // Organizational data structure
        const orgData = [
            {
                direction: "Direction Générale",
                departments: [
                    {
                        name: "Affaires Juridiques & Contentieux",
                        services: []
                    },
                    {
                        name: "Audit Interne & Contrôle de Gestion",
                        services: []
                    },
                    {
                        name: "Commerciale & Marketing",
                        services: [
                            "Support Commercial & Relations SNTL",
                            "Gestion Réseau Propre et Partenaires",
                            "Gestion Grands Comptes Produits Blancs et Lubrifiants",
                            "Business Unit Rihab Restauration & Shops"
                        ]
                    },
                    {
                        name: "Supply Chain",
                        services: [
                            "Production Blending",
                            "Expéditions & Logistique",
                            "Laboratoire"
                        ]
                    },
                    {
                        name: "Technique Maintenance & HSE",
                        services: [
                            "Maintenance & HSE",
                            "Projets Stations-service"
                        ]
                    },
                    {
                        name: "Ressources Humaines",
                        services: [
                            "Administration QHSE"
                        ]
                    },
                    {
                        name: "Moyens Généraux",
                        services: [
                            "Achats"
                        ]
                    },
                    {
                        name: "Finances",
                        services: [
                            "Comptabilité",
                            "Crédit Management",
                            "Trésorerie",
                            "Back Office"
                        ]
                    },
                    {
                        name: "Système d'information",
                        services: []
                    }
                ]
            }
        ];

        // Flatten data for searching
        let flattenedData = [];

        function flattenOrgData() {
            flattenedData = [];
            orgData.forEach(direction => {
                direction.departments.forEach(dept => {
                    if (dept.services.length === 0) {
                        // Département without services
                        flattenedData.push({
                            display: `${direction.direction} > ${dept.name}`,
                            value: `${direction.direction} > ${dept.name}`,
                            searchText: `${direction.direction} ${dept.name}`.toLowerCase()
                        });
                    } else {
                        // Services under département
                        dept.services.forEach(service => {
                            flattenedData.push({
                                display: `${direction.direction} > ${dept.name} > ${service}`,
                                value: `${direction.direction} > ${dept.name} > ${service}`,
                                searchText: `${direction.direction} ${dept.name} ${service}`.toLowerCase()
                            });
                        });
                    }
                });
            });
        }

        // Initialize
        flattenOrgData();

        // DOM elements
        const orgInput = document.getElementById('orgInput');
        const dropdown = document.getElementById('dropdown');
        const clearBtn = document.getElementById('clearBtn');
        const form = document.getElementById('orgForm');
        const selectedValue = document.getElementById('selectedValue');
        const selectedContent = document.getElementById('selectedContent');
        const submitBtn = document.getElementById('submitBtn');

        let currentSelectionValue = null;
        let currentIndex = -1;

        // Filter and display suggestions
        function filterAndDisplay(searchTerm) {
            const trimmed = searchTerm.toLowerCase().trim();
            
            if (trimmed.length === 0) {
                dropdown.classList.remove('show');
                return;
            }

            const filtered = flattenedData.filter(item =>
                item.searchText.includes(trimmed)
            );

            if (filtered.length === 0) {
                dropdown.innerHTML = '<div class="no-results">Aucun résultat trouvé</div>';
                dropdown.classList.add('show');
                return;
            }

            dropdown.innerHTML = filtered.map((item, index) => `
                <div class="dropdown-item" data-value="${item.value}" data-index="${index}">
                    <i class="fas fa-folder-open" style="color: #4CAF50; font-size: 14px;"></i>
                    <div class="item-hierarchy">
                        <span>${item.display}</span>
                    </div>
                </div>
            `).join('');

            dropdown.classList.add('show');
            currentIndex = -1;

            // Attach click handlers
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', () => selectItem(item));
            });
        }

        // Select item from dropdown
        function selectItem(element) {
            const value = element.getAttribute('data-value');
            currentSelectionValue = value;
            orgInput.value = value;
            dropdown.classList.remove('show');
            clearBtn.classList.add('show');
            submitBtn.disabled = false;

            // Show selected value
            selectedContent.textContent = value;
            selectedValue.classList.add('show');
        }

        // Clear input
        function clearInput() {
            orgInput.value = '';
            currentSelectionValue = null;
            currentIndex = -1;
            dropdown.innerHTML = '';
            dropdown.classList.remove('show');
            clearBtn.classList.remove('show');
            submitBtn.disabled = true;
            selectedValue.classList.remove('show');
            selectedContent.textContent = '';
            orgInput.focus();
        }

        // Event listeners
        orgInput.addEventListener('input', (e) => {
            const value = e.target.value;
            if (value.length > 0) {
                clearBtn.classList.add('show');
            } else {
                clearBtn.classList.remove('show');
                currentSelectionValue = null;
                submitBtn.disabled = true;
                selectedValue.classList.remove('show');
            }
            filterAndDisplay(value);
        });

        orgInput.addEventListener('focus', () => {
            if (orgInput.value.length > 0) {
                filterAndDisplay(orgInput.value);
            }
        });

        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            clearInput();
        });

        // Keyboard navigation
        orgInput.addEventListener('keydown', (e) => {
            const items = document.querySelectorAll('.dropdown-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, -1);
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (currentIndex >= 0 && items[currentIndex]) {
                    selectItem(items[currentIndex]);
                } else if (currentSelectionValue) {
                    form.dispatchEvent(new Event('submit'));
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('show');
            }
        });

        function updateActiveItem(items) {
            items.forEach((item, idx) => {
                item.classList.toggle('active', idx === currentIndex);
            });

            if (currentIndex >= 0) {
                items[currentIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.input-wrapper')) {
                dropdown.classList.remove('show');
            }
        });

        // Form submission
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (currentSelectionValue) {
                console.log('Selected:', currentSelectionValue);
                alert(`Sélection confirmée:\n\n${currentSelectionValue}`);
                // Here you would typically send the data to the server
            }
        });
    </script>
</body>
</html>
