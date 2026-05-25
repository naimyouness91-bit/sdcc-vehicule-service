<!-- Service Dropdown (re-usable) -->
@php
    $selectName = $selectName ?? 'service';
    $selectId = $selectId ?? $selectName;
    $selected = old($selectName, isset($user) ? $user->service : '');
    $isRequired = isset($required) && $required ? 'required' : '';
    // Normalise la valeur sélectionnée pour comparer insensible à la casse
    $normalizedSelected = (string) $selected;
    $normalizedSelected = str_replace('’', "'", $normalizedSelected);
    $normalizedSelected = mb_strtolower($normalizedSelected);
    function _opt_eq($normalizedSelected, $option) {
        return $normalizedSelected === mb_strtolower(str_replace('’', "'", (string) $option));
    }
@endphp

<select id="{{ $selectId }}" name="{{ $selectName }}" class="form-control @error($selectName) is-invalid @enderror" {{ $isRequired }}>
    <option value="">-- Sélectionnez un service --</option>

    <optgroup label="Direction Générale">
        <option value="Secrétariat de Direction Générale" @selected(_opt_eq($normalizedSelected, 'Secrétariat de Direction Générale'))>Secrétariat de Direction Générale</option>
        <option value="Audit Interne & Contrôle de Gestion" @selected(_opt_eq($normalizedSelected, 'Audit Interne & Contrôle de Gestion'))>Audit Interne & Contrôle de Gestion</option>
        <option value="Affaires Juridiques & Contentieux" @selected(_opt_eq($normalizedSelected, 'Affaires Juridiques & Contentieux'))>Affaires Juridiques & Contentieux</option>
    </optgroup>

    <optgroup label="Commerciale & Marketing">
        <option value="Support Commercial & Relations SNTL" @selected(_opt_eq($normalizedSelected, 'Support Commercial & Relations SNTL'))>Support Commercial & Relations SNTL</option>
        <option value="Gestion Réseau Propre et Partenaires" @selected(_opt_eq($normalizedSelected, 'Gestion Réseau Propre et Partenaires'))>Gestion Réseau Propre et Partenaires</option>
        <option value="Gestion Grands Comptes Produits Blancs et Lubrifiants" @selected(_opt_eq($normalizedSelected, 'Gestion Grands Comptes Produits Blancs et Lubrifiants'))>Gestion Grands Comptes Produits Blancs et Lubrifiants</option>
        <option value="Business Unit Rihab Restauration & Shops" @selected(_opt_eq($normalizedSelected, 'Business Unit Rihab Restauration & Shops'))>Business Unit Rihab Restauration & Shops</option>
    </optgroup>

    <optgroup label="Supply Chain">
        <option value="Production Blending" @selected(_opt_eq($normalizedSelected, 'Production Blending'))>Production Blending</option>
        <option value="Expéditions & Logistique" @selected(_opt_eq($normalizedSelected, 'Expéditions & Logistique'))>Expéditions & Logistique</option>
        <option value="Laboratoire" @selected(_opt_eq($normalizedSelected, 'Laboratoire'))>Laboratoire</option>
    </optgroup>

    <optgroup label="Technique, Maintenance & HSE">
        <option value="Maintenance & HSE" @selected(_opt_eq($normalizedSelected, 'Maintenance & HSE'))>Maintenance & HSE</option>
        <option value="Projets Stations-service" @selected(_opt_eq($normalizedSelected, 'Projets Stations-service'))>Projets Stations-service</option>
    </optgroup>

    <optgroup label="Ressources Humaines">
        <option value="Administration QHSE & Amélioration Continue" @selected(_opt_eq($normalizedSelected, 'Administration QHSE & Amélioration Continue'))>Administration QHSE & Amélioration Continue</option>
        <option value="Moyens Généraux" @selected(_opt_eq($normalizedSelected, 'Moyens Généraux'))>Moyens Généraux</option>
        <option value="Achats" @selected(_opt_eq($normalizedSelected, 'Achats'))>Achats</option>
    </optgroup>

    <optgroup label="Finances">
        <option value="Comptabilité" @selected(_opt_eq($normalizedSelected, 'Comptabilité'))>Comptabilité</option>
        <option value="Crédit Management & Administration des Ventes" @selected(_opt_eq($normalizedSelected, 'Crédit Management & Administration des Ventes'))>Crédit Management & Administration des Ventes</option>
        <option value="Trésorerie" @selected(_opt_eq($normalizedSelected, 'Trésorerie'))>Trésorerie</option>
        <option value="Back Office Ventes monétiques" @selected(_opt_eq($normalizedSelected, 'Back Office Ventes monétiques'))>Back Office Ventes monétiques</option>
        <option value="Système d'Information" @selected(_opt_eq($normalizedSelected, "Système d'Information"))>Système d'Information</option>
    </optgroup>
</select>
@error($selectName)
    <div class="error-message">{{ $message }}</div>
@enderror
