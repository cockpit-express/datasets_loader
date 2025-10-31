<?php
  namespace Script;

  use Model\Place;

  class PlacesImport {
    public static function run(): void {
      $file = __DIR__ . '/../dataset/basilic-dataculture-2025-09-05.csv';
      $count = 0;

      $ignoredTypes = [
        "Établissement d'enseignement supérieur", 
        "Papeterie et maisons de la presse",
        "Bibliothèque",
        "Centre de création artistique",
        "Centre de création musicale",
        "Cinéma",
        "Conservatoire",
        "Librairie",
        "Scène",
        "Service d'archives"
      ];

      $ignoredLabels = [
        "Architecture contemporaine remarquable",
        "Microfolie",
        "Site patrimonial remarquable"
      ];

      $importantLabels = [
        "Patrimoine mondial de l'Unesco",
        "Centre d’art contemporain d’intérêt national",
        "Zénith"
      ];

      $includedKeywords = [
        // Religieux
        'cathédrale', 'basilique', 'abbaye', 'prieuré', 'monastère', 'cloître',
        'église', 'temple', 'mosquée', 'synagogue', 'dieu',

        // Défense
        'château', 'donjon', 'fort', 'forteresse', 'citadelle', 'bastion',
        'tour', 'rempart', 'enceinte', 'muraille', 'caserne', 'arsenal', 'blockhaus',
        'palais',

        // Civil / antique
        'aqueduc', 'pont', 'amphithéâtre', 'arènes', 'théâtre antique', 'thermes',
        'ruine', 'dolmen', 'menhir', 'tumulus', 'oppidum', 'nécropole', 'crypte',
        'catacombes', 'beffroi', 'clocher', 'hôtel particulier',

        // Technique / industriel
        'moulin', 'phare', 'mine', 'puits', 'forge', 'carrière', 'usine', 'canal', 'écluse',

        // Autres
        'gare historique', 'maison natale', 'villa', 'palais', 'enceinte gallo-romaine'
      ];

      $excludedKeywords = [
        // Commémoratif mineur
        'croix', 'croix de chemin', 'calvaire', 'oratoire', 'chapelle', 'stèle',
        'plaque', 'plaque commémorative', 'monument aux morts', 'tombe', 'tombeau',
        'cimetière', 'mémorial',

        // Infrastructure mineure
        'fontaine', 'lavoir', 'borne', 'borne kilométrique', 'borne géodésique',
        'cadran solaire', 'horloge', 'puits', 'citerne',

        // Décoratifs
        'statue', 'sculpture', 'buste', 'bas-relief', 'portail', 'façade',
        'colonnade', 'rotonde',

        // Bâtiments mineurs
        'mairie', 'hôtel de ville', 'préfecture', 'tribunal'
      ];

      if (!file_exists($file)) {
        echo "❌ File not found: {$file}\n";
        return;
      }

      if (($handle = fopen($file, 'r')) !== false) {
        $count = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
          $name = trim($row[1] ?? '');
          $type = trim($row[9] ?? '');
          $label = trim($row[10] ?? '');
          $postal_code = trim($row[4] ?? '');
          $city = trim($row[5] ?? '');
          $address = trim($row[2] ?? '');
          $latitude = isset($row[45]) ? floatval($row[45]) : null;
          $longitude = isset($row[46]) ? floatval($row[46]) : null;
          $region_code = trim($row[19] ?? '');
          $departement_code = trim($row[18] ?? '');

          if (
            (
              in_array($type, $ignoredTypes) ||
              in_array($label, $ignoredLabels)
            ) &&
            !in_array($label, $importantLabels)
          ) {
            echo "⏩ Skipped {$name} because of type ({$type})" . PHP_EOL;
            continue;
          }

          if (strtolower($type) === 'monument') {
            $nameLower = strtolower($name);

            // Mots clé non pertinents
            foreach ($excludedKeywords as $kw) {
              if (str_contains($nameLower, $kw)) {
                echo "⏩ Skipped {$name} (excluded keyword: {$kw})\n";
                continue 2; 
              }
            }

            // Mots clé pertinents
            $keep = false;
            foreach ($includedKeywords as $kw) {
              if (str_contains($nameLower, $kw)) {
                $keep = true;
                break;
              }
            }

            if (!$keep) {
              echo "⏩ Skipped {$name} (generic monument)\n";
              continue;
            }

            // Focus Eglises / chapelles
            if (preg_match('/\b(église|chapelle)\b/i', $name)) {
              if (!preg_match('/(saint|sainte|notre[- ]dame|classée|royale|du|de la|de l\')/i', $name)) {
                echo "⏩ Skipped {$name} (generic church/chapel)\n";
                continue;
              }
            }
          }

          if (!$name || !$latitude || !$longitude) {
            echo "⏩ Skipped {$name} because lack of data\n";
            continue;
          }

          Place::firstOrCreate(
            [
              'name' => $name,
              'address' => $address, 
              'postal_code' => $postal_code,
              'type' => $type,
              'label' => $label,
              'city' => $city,
              'latitude' => $latitude,
              'longitude' => $longitude,
              'region_code' => $region_code,
              'departement_code' => $departement_code,
            ]
          );

          $count++;
          echo "Import {$name}\n";
        }

        fclose($handle);
        echo "✅ Import done ({$count} rows)";

      } else {
        echo "❌ Impossible import: cannot open CSV file";
      }
    }
  }
