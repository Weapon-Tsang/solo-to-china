/* Only loaded by Place Finder. No network access or persistent image storage. */
self.onmessage = async function (event) {
    var file = event.data, bitmap;
    try {
        if (!['image/jpeg','image/png','image/webp'].includes(file.type)) { throw new Error('Use a JPG, PNG or WebP image. For HEIC, take a screenshot first.'); }
        if (!file.size || file.size > 20971520) { throw new Error('Each source photo must be 20 MB or smaller.'); }
        bitmap = await createImageBitmap(file, {imageOrientation:'from-image'});
        var width=bitmap.width, height=bitmap.height;
        if (!width || !height || width*height>40000000 || Math.max(width,height)>12000) { throw new Error('This image has too many pixels. Crop or resize it before trying again.'); }
        var textImage=file.type==='image/png' || Math.max(width,height)/Math.min(width,height)>3;
        var scale=Math.min(1,(textImage?4096:2048)/Math.max(width,height),Math.sqrt(6000000/(width*height)));
        var canvas=new OffscreenCanvas(Math.max(1,Math.round(width*scale)),Math.max(1,Math.round(height*scale)));
        var ctx=canvas.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.drawImage(bitmap,0,0,canvas.width,canvas.height);
        var blob=await canvas.convertToBlob({type:textImage?'image/webp':'image/jpeg',quality:textImage?.92:.86});
        if (blob.size>2500000) { throw new Error('This image is still too large after preparation. Crop the important area and try again.'); }
        self.postMessage({blob:blob,width:canvas.width,height:canvas.height});
    } catch(error) { self.postMessage({error:error.message || 'This image could not be decoded. Try another photo.'}); }
    finally { if(bitmap){bitmap.close();} }
};
