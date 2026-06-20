import sys, sqlite3, os
sys.stdout.reconfigure(encoding='utf-8')

db_path = r'D:\SAPIENS\source\database\database.sqlite'
img_dir = r'D:\SAPIENS\source\public\images\menu'

mapping = {
    'Trio Potato Mille-Feuille': 'images/menu/trio-potato-mille-feuille.jpg',
    "Hunter's Roll":              'images/menu/hunters-roll.jpg',
    'Not Squid':                  'images/menu/not-squid.jpg',
    'Ocean Ruby':                 'images/menu/ocean-ruby.jpg',
    'Prawn Nachos & Burrata':     'images/menu/prawn-nachos-burrata.jpg',
    'Ocean Greens Salad':         'images/menu/ocean-greens-salad.jpg',
    'Octopus Yakitori':           'images/menu/octopus-yakitori.jpg',
    'Aburi Salmon Pani Puri':     'images/menu/aburi-salmon-pani-puri.jpg',
    'Torched Salmon & Prawn Roulade': 'images/menu/torched-salmon-prawn-roulade.jpg',
    'Ivory Cloud':                'images/menu/ivory-cloud.jpg',
}

conn = sqlite3.connect(db_path)
cur = conn.cursor()

for name_en, path in mapping.items():
    cur.execute("UPDATE menu_items SET image_path = ? WHERE name_en = ?", (path, name_en))
    print(f'Updated {name_en}: {cur.rowcount} row(s)')

conn.commit()
conn.close()
print('Done.')
