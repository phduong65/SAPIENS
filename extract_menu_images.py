import sys, os
sys.stdout.reconfigure(encoding='utf-8')
from pypdf import PdfReader
from PIL import Image
import io

pdf_path = r'C:\Users\Admin\Downloads\SAPIENS_MENU (draft).pdf'
out_dir = r'D:\SAPIENS\source\public\images\menu'
os.makedirs(out_dir, exist_ok=True)

item_names = [
    'trio-potato-mille-feuille',
    'hunters-roll',
    'not-squid',
    'ocean-ruby',
    'prawn-nachos-burrata',
    'ocean-greens-salad',
    'octopus-yakitori',
    'aburi-salmon-pani-puri',
    'torched-salmon-prawn-roulade',
    'ivory-cloud',
]

def to_rgb(img):
    if img.mode == 'RGBA':
        bg = Image.new('RGB', img.size, (26, 26, 24))  # cave-black background
        bg.paste(img, mask=img.split()[3])
        return bg
    elif img.mode == 'CMYK':
        return img.convert('RGB')
    elif img.mode != 'RGB':
        return img.convert('RGB')
    return img

reader = PdfReader(pdf_path)

for page_idx, page in enumerate(reader.pages):
    name = item_names[page_idx] if page_idx < len(item_names) else f'item-{page_idx+1}'
    saved = False

    for img_idx, img_obj in enumerate(page.images):
        try:
            pil_img = Image.open(io.BytesIO(img_obj.data))
            w, h = pil_img.size
            print(f'Page {page_idx+1} img{img_idx}: {w}x{h} mode={pil_img.mode}')

            # The tall 1299x3189 images: each page has dish photo in a vertical strip
            # Crop the top portion (dish) — roughly the top 40% is the food photo
            if w < 1350 and h > 2000:
                pil_img = to_rgb(pil_img)
                # Crop top portion (food photo area ~top 1/3)
                crop_h = int(h * 0.38)
                cropped = pil_img.crop((0, 0, w, crop_h))
                # Resize to reasonable web size
                cropped = cropped.resize((600, int(600 * crop_h / w)), Image.LANCZOS)
                out_path = os.path.join(out_dir, f'{name}.jpg')
                cropped.save(out_path, 'JPEG', quality=85, optimize=True)
                print(f'  -> Saved dish: {name}.jpg ({cropped.size})')
                saved = True
                break

        except Exception as e:
            print(f'  Error page {page_idx+1} img{img_idx}: {e}')

    if not saved:
        print(f'  WARNING: No image saved for page {page_idx+1} ({name})')

print('\nDone. Files in:', out_dir)
for f in os.listdir(out_dir):
    size = os.path.getsize(os.path.join(out_dir, f))
    print(f'  {f} ({size//1024}KB)')
