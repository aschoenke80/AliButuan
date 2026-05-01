const { Jimp } = require('jimp');
const path = require('path');

const input  = path.join(__dirname, '../public/images/logo.png');
const output = path.join(__dirname, '../public/images/logo.png');

Jimp.read(input).then(img => {
    // Make black/near-black pixels transparent
    img.scan(0, 0, img.bitmap.width, img.bitmap.height, function(x, y, idx) {
        const r = this.bitmap.data[idx + 0];
        const g = this.bitmap.data[idx + 1];
        const b = this.bitmap.data[idx + 2];
        if (r < 40 && g < 40 && b < 40) {
            this.bitmap.data[idx + 3] = 0;
        }
    });

    img.autocrop({ tolerance: 0.02, cropOnlyFrames: false });

    return img.write(output);
}).then(() => {
    console.log('Done! Black background removed and cropped.');
}).catch(err => {
    console.error(err);
});
