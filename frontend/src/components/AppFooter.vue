<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'

const theme = useTheme()

const isDarkMode = computed(
  () => theme.global.name.value === 'villaThemeDark'
)
</script>

<template>
  <footer class="sneat-footer" :style="{ backgroundColor: isDarkMode ? '#25253b' : '#f5f5f9' }">
    <!-- Main Footer Content -->
    <section class="footer-content">
      <div class="container">
        <div class="footer-grid">
          <!-- Brand Section -->
          <div class="footer-column">
            <h4 class="footer-title">Villa Project</h4>
            <p class="footer-desc">
              Plateforme de location de villas premium. Découvrez des hébergements 
              exceptionnels dans le monde entier.
            </p>
            <div class="social-links">
              <a href="#" class="social-icon" aria-label="Facebook">
                <v-icon size="small">mdi-facebook</v-icon>
              </a>
              <a href="#" class="social-icon" aria-label="Instagram">
                <v-icon size="small">mdi-instagram</v-icon>
              </a>
              <a href="#" class="social-icon" aria-label="Twitter">
                <v-icon size="small">mdi-twitter</v-icon>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div class="footer-column">
            <h4 class="footer-title">Navigation</h4>
            <ul class="footer-links">
              <li><router-link to="/">Accueil</router-link></li>
              <li><router-link to="/">Parcourir les villas</router-link></li>
              <li><router-link to="/">Comment ça marche</router-link></li>
              <li><router-link to="/">Contact</router-link></li>
            </ul>
          </div>

          <!-- Support -->
          <div class="footer-column">
            <h4 class="footer-title">Assistance</h4>
            <ul class="footer-links">
              <li><a href="#">Centre d'aide</a></li>
              <li><a href="#">Conditions d'utilisation</a></li>
              <li><a href="#">Politique de confidentialité</a></li>
              <li><a href="#">Politique de cookies</a></li>
            </ul>
          </div>

          <!-- Contact -->
          <div class="footer-column">
            <h4 class="footer-title">Contact</h4>
            <div class="contact-info">
              <p><v-icon size="small" class="me-2">mdi-email</v-icon>support@villaproject.com</p>
              <p><v-icon size="small" class="me-2">mdi-phone</v-icon>+1 (555) 000-0000</p>
              <p><v-icon size="small" class="me-2">mdi-map-marker</v-icon>123 Rue Principale, Ville</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom Bar -->
    <div class="footer-bottom" :style="{ borderTopColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)' }">
      <div class="container">
        <p class="copyright">
          © {{ new Date().getFullYear() }} Villa Project. Tous droits réservés.
        </p>
        <p class="build-info">
          Build du {{ new Date().toLocaleDateString('fr-FR') }}
        </p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.sneat-footer {
  transition: all 0.3s ease;
}

.footer-content {
  padding: 3rem 0;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.footer-column {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.footer-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--sneat-title, #566a7f);
  margin-bottom: 0.5rem;
}

.footer-desc {
  font-size: 14px;
  color: var(--sneat-text, #697a8d);
  line-height: 1.6;
}

.social-links {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: rgba(105, 108, 255, 0.1);
  color: #696cff;
  text-decoration: none;
  transition: all 0.3s ease;
}

.social-icon:hover {
  background: #696cff;
  color: white;
  transform: translateY(-2px);
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  color: var(--sneat-text, #697a8d);
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s ease;
}

.footer-links a:hover {
  color: #696cff;
  padding-left: 0.5rem;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-info p {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: var(--sneat-text, #697a8d);
  margin: 0;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 0;
  border-top: 1px solid;
  font-size: 12px;
  color: var(--sneat-text, #697a8d);
}

.copyright {
  margin: 0;
}

.build-info {
  margin: 0;
  font-size: 11px;
}

@media (max-width: 768px) {
  .summary-cards {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
  }

  .footer-bottom {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}

/* Dark mode support */
:deep(.sneat-footer) {
  --sneat-bg: #f5f5f9;
  --sneat-surface: #ffffff;
  --sneat-border: #e7e7ef;
  --sneat-title: #566a7f;
  --sneat-text: #697a8d;
}

:deep(.sneat-footer.dark-mode) {
  --sneat-bg: #25253b;
  --sneat-surface: #2d2e44;
  --sneat-border: #42435f;
  --sneat-title: #b8bcc8;
  --sneat-text: #7f88a0;
}
</style>
