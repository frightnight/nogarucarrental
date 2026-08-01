const fs = require('fs');
const path = require('path');
const filePath = path.join(__dirname, 'resources', 'views', 'landing.blade.php');
let text = fs.readFileSync(filePath, 'utf8');
text = text.replace(/(href|src)="assets\/([^"]*)"/g, (match, attr, file) => {
  return `${attr}="{{ asset('assets/${file}') }}"`;
});
text = text.replace(/url\(assets\/([^\)]+)\)/g, (match, file) => {
  return `url({{ asset('assets/${file}') }})`;
});
text = text.replace(/href="index\.html"/g, 'href="{{ url('/') }}"');
text = text.replace(/href="auth-sign-in\.html"/g, 'href="{{ route(\'login\') }}"');
text = text.replace(/href="auth-sign-up\.html"/g, 'href="{{ route(\'register\') }}"');
fs.writeFileSync(filePath, text, 'utf8');
console.log('done');
