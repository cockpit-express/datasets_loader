# Datasets loader

## Dataset

Because of their weight, the two `csv` datasets are not included in the repository. You can get them here :  
- Train stations : https://defis.data.gouv.fr/datasets/65d81858179dc96581d981db 
- Touristic places : https://defis.data.gouv.fr/datasets/61777ddaa9101d073e5506cd  

## Libraries

`illuminate/database` (Eloquent ORM)
`vlucas/phpdotenv`
`phpoffice/phpspreadsheet`

## Project tree

```
saews303d_datasets_loader
├─ bootstrap.php
├─ composer.json
├─ composer.lock
└─ src
   ├─ dataset
   │  ├─ basilic-dataculture-2025-09-05.csv
   │  └─ gares-de-voyageurs.csv
   ├─ index.php
   ├─ model
   │  ├─ Place.php
   │  └─ Station.php
   └─ scripts
      ├─ PlacesImport.php
      └─ StationsImport.php
```