// routes/auth.js
const express = require('express');
const router = express.Router();

const { isGuest, isAuthenticated } = require('../middlewares/auth');
const { loginThrottle } = require('../middlewares/rateLimiter');
const { validateSignature } = require('../middlewares/signed');

// Controllers
const registeredUserController = require('../controllers/auth/registeredUser');
const authenticatedSessionController = require('../controllers/auth/AuthentificatedSesssionController');
const passwordResetLinkController = require('../controllers/auth/passwordResetLink');
const newPasswordController = require('../controllers/auth/newPassword');
const emailVerificationPromptController = require('../controllers/auth/emailVerificationPrompt');
const verifyEmailController = require('../controllers/auth/VerifyEmail');
const emailVerificationNotificationController = require('../controllers/auth/emailVerificationNotification');
const confirmablePasswordController = require('../controllers/auth/ConfirmablePassword');
const passwordController = require('../controllers/auth/Password');

// ==========================
// Routes invité (guest)
// ==========================
router.get('/register', isGuest, registeredUserController.showForm);
router.post('/register', isGuest, registeredUserController.store);

router.get('/login', isGuest, loginThrottle, authenticatedSessionController.showLogin);
router.post('/login', isGuest, loginThrottle, authenticatedSessionController.login);


router.post('/forgot-password', isGuest, passwordResetLinkController.store);

router.post('/reset-password', isGuest, newPasswordController.store);

// ==========================
// Routes protégées (auth)
// ==========================
router.get('/me', isAuthenticated, authenticatedSessionController.me);

router.get('/verify-email', isAuthenticated, emailVerificationPromptController.prompt);

router.get(
  '/verify-email/:id/:hash',
  isAuthenticated,
  validateSignature,
  verifyEmailController.verify
);

router.post(
  '/email/verification-notification',
  isAuthenticated,
  emailVerificationNotificationController.store
);

router.get('/confirm-password', isAuthenticated, confirmablePasswordController.show);
router.post('/confirm-password', isAuthenticated, confirmablePasswordController.store);

router.put('/password', isAuthenticated, passwordController.update);

router.post('/logout', isAuthenticated, authenticatedSessionController.logout);

module.exports = router;
