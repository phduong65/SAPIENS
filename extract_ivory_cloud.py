import sys
sys.stdout.reconfigure(encoding='utf-8')
from pypdf import PdfReader
from PIL import Image
import io, os

pdf_path = r'C:\Users\Admin\Downloads\SAPIENS_MENU (draft).pdf'
out_dir = r'D:\SAPIENS\source\public\images\menu'

reader = PdfReader(pdf_path)
page = reader.pages[9]  # page 10 = ivory-cloud

for img_idx, img_obj in enumerate(page.images):
    pil_img = Image.open(io.BytesIO(img_obj.data))
    w, h = pil_img.size
    print(f'img{img_idx}: {w}x{h} mode={pil_img.mode}')

    if pil_img.mode == 'RGBA':
        bg = Image.new('RGB', pil_img.size, (26, 26, 24))
        bg.paste(pil_img, mask=pil_img.split()[3])
        pil_img = bg
    else:
        pil_img = pil_img.convert('RGB')

    # For wider image crop top area as food photo
    crop_h = int(h * 0.45)
    cropped = pil_img.crop((0, 0, w, crop_h))
    cropped = cropped.resize((600, int(600 * crop_h / w)), Image.LANCZOS)
    out = os.path.join(out_dir, 'ivory-cloud.jpg')
    cropped.save(out, 'JPEG', quality=85)
    print(f'Saved ivory-cloud.jpg {cropped.size}')
    break
