const multer = require("multer");

// On garde les fichiers en mémoire (pas dans /uploads)
const storage = multer.memoryStorage();
const upload = multer({ storage });

module.exports = upload;
