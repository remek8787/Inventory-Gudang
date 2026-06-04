# Inventory Gudang — Denta Sejahtera Group

Project legacy PHP native/XAMPP yang dimigrasikan ke hosting:

- Live: https://inventory.dentasejahteragroup.my.id/
- Blueprint: `docs/BLUEPRINT.md`
- SQL awal: `database/gudangv1-452026.sql`
- Config DB template: `belanja/db.php.example`

## Setup Singkat

1. Buat database MySQL.
2. Import `database/gudangv1-452026.sql`.
3. Copy `belanja/db.php.example` menjadi `belanja/db.php`.
4. Isi credential database di `belanja/db.php`.
5. Upload ke root subdomain/hosting.

## Catatan

Jangan commit `belanja/db.php` asli karena berisi credential database.

## Maintenance Notes

- Live changes must follow backup-before-edit.
- Do not commit `belanja/db.php` or any credential.
- Operational/live incident details are tracked in `docs/BLUEPRINT.md`.
- Latest important incident: 2026-06-04 fixed typo stock `ISOLASI` row `DNACS67172` from `67172` to `1`; backup JSON stored in workspace state.
