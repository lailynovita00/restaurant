# Theme Color Changes - Palombini Cafe Restaurant

## Color Palette Update

Tema warna website telah diubah dari **Red/Pink Theme** menjadi **Brown/Coffee Theme**.

### Pemetaan Warna

| Elemen | Warna Lama | Warna Baru | Kode Hex |
|--------|-----------|-----------|---------|
| Primary Color (Buttons, Links Hover) | #FF324D (Merah) | #92824e | Olive Brown |
| Text - Heading | #222222 (Hitam) | #50301c | Dark Brown |
| Text - Body | #687188 (Abu-abu) | #765e39 | Medium Brown |
| Dark Elements | #292b2c (Hitam) | #50301c | Dark Brown |

### Palet Lengkap

```
CSV Format: 92824e,50301c,f1ddb9,cbb88c,765e39,a38a6a,5e4127,887446

Array Format: ["92824e","50301c","f1ddb9","cbb88c","765e39","a38a6a","5e4127","887446"]
```

#### Penjelasan Warna:
- **#92824e** - Primary color (main brown/olive)
- **#50301c** - Primary dark (dark brown untuk text)
- **#f1ddb9** - Light cream/beige (background accent)
- **#cbb88c** - Tan/beige (secondary)
- **#765e39** - Medium brown (untuk text secondary)
- **#a38a6a** - Light brown (accent)
- **#5e4127** - Very dark brown (footer, dark elements)
- **#887446** - Gold/tan (special accents)

## File yang Diubah

### 1. **public/assets/css/style.css**
- Semua reference warna #FF324D diubah ke #92824e
- Semua reference warna #222222 diubah ke #50301c
- Semua reference warna #687188 diubah ke #765e39
- Semua reference warna #292b2c diubah ke #50301c

### 2. **tailwind.config.js**
- Added custom color palette untuk Tailwind CSS
- Colors dapat digunakan dengan class: `bg-primary`, `text-primary`, dll

### 3. **resources/css/theme-colors.css** (New)
- File baru untuk CSS variables dan utility classes
- Memudahkan maintenance dan konsistensi warna di masa depan
- Berisi theme colors dan button styles

## Cara Menggunakan Warna Baru

### Di CSS:
```css
.my-element {
  color: #92824e;  /* Primary color */
  background-color: #50301c;  /* Dark brown */
}
```

### Di Tailwind (blade.php):
```html
<!-- Background -->
<div class="bg-primary">Primary color background</div>

<!-- Text -->
<p class="text-primary">Primary text color</p>

<!-- Buttons -->
<button class="btn-theme">Brown Button</button>
```

### Di CSS Variables (theme-colors.css):
```css
h1 {
  color: var(--color-primary);
  background-color: var(--color-primary-light);
}
```

## Element yang Terupdate

✅ **Navigation Bar** - Link hover color
✅ **Buttons** - Primary buttons, button hover states
✅ **Text** - Headings, body text, labels
✅ **Links** - Link hover states
✅ **Components** - Cards, icons, highlights
✅ **Forms** - Buttons, focus states
✅ **Footer** - Links, icons
✅ **Pagination** - Active state, hover
✅ **Badges** - Primary badges
✅ **Indicators** - Carousel indicators

## Testing

Untuk memastikan semua warna sudah terupdate dengan benar:

1. Buka website di browser
2. Hover pada link/button - should show brown color (#92824e)
3. Cek heading text - should be dark brown (#50301c)
4. Cek body text - should be medium brown (#765e39)

## Rollback (Jika diperlukan)

Jika perlu kembali ke warna lama, bisa restore dari git atau ganti warna balik ke:
- #FF324D (for #92824e)
- #222222 (for #50301c)
- #687188 (for #765e39)
- #292b2c (for #50301c)

---
**Updated:** February 17, 2026
**Theme:** Coffee/Brown Palette
