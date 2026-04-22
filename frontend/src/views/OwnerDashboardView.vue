<template>
  <div class="app-layout">

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
      <div class="sidebar-logo">
        <div class="logo-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1H4a1 1 0 01-1-1V10.5z" fill="rgba(255,255,255,0.95)" stroke="white" stroke-width="1.5"/>
            <rect x="8" y="14" width="4" height="8" rx="0.5" fill="#6366f1"/>
            <rect x="13" y="12" width="4" height="4" rx="0.5" fill="#6366f1" opacity="0.8"/>
          </svg>
        </div>
        <transition name="fade-text">
          <div v-if="!sidebarCollapsed" class="logo-text">
            <span class="logo-name">VillaHost</span>
            <span class="logo-sub">Gestionnaire</span>
          </div>
        </transition>
        <button class="collapse-btn" @click="sidebarCollapsed = !sidebarCollapsed" :title="sidebarCollapsed ? 'Agrandir' : 'Réduire'">
          <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.8" stroke-linecap="round">
            <path v-if="!sidebarCollapsed" d="M9 2L4 7l5 5"/>
            <path v-else d="M5 2l5 5-5 5"/>
          </svg>
        </button>
      </div>

      <!-- Nav label -->
      <div v-if="!sidebarCollapsed" class="nav-section-label">Navigation</div>

      <nav class="sidebar-nav">
        <div
          v-for="item in navItems"
          :key="item.key"
          class="nav-item"
          :class="{ active: activeNav === item.key }"
          @click="setActiveNav(item.key)"
          :title="sidebarCollapsed ? item.label : ''"
        >
          <span class="nav-icon" v-html="item.icon"></span>
          <span v-if="!sidebarCollapsed" class="nav-label">{{ item.label }}</span>
          <span v-if="!sidebarCollapsed && item.badge" class="nav-badge">{{ item.badge }}</span>
        </div>
      </nav>

      <!-- Divider -->
      <div class="sidebar-divider"></div>

      <!-- Bottom actions -->
      <div class="sidebar-bottom">
        <div class="nav-item bottom-item" @click="showProfile = true" :title="sidebarCollapsed ? 'Voir profil' : ''">
          <span class="nav-icon">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="3"/><path d="M2 14c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke-linecap="round"/></svg>
          </span>
          <span v-if="!sidebarCollapsed" class="nav-label">Voir profil</span>
        </div>
        <div class="nav-item bottom-item logout" @click="handleLogout" :title="sidebarCollapsed ? 'Déconnexion' : ''">
          <span class="nav-icon">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 2H3a1 1 0 00-1 1v10a1 1 0 001 1h3M10 11l3-3-3-3M13 8H6"/></svg>
          </span>
          <span v-if="!sidebarCollapsed" class="nav-label">Déconnexion</span>
        </div>
      </div>

      <!-- User card -->
      <div class="sidebar-user">
        <div class="user-avatar">{{ userInitial }}</div>
        <transition name="fade-text">
          <div v-if="!sidebarCollapsed" class="user-info">
            <span class="user-name">{{ user.name }}</span>
            <span class="user-email">{{ user.email }}</span>
          </div>
        </transition>
      </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="main-wrapper">

      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <h1 class="page-title">{{ currentPageTitle }}</h1>
          <div class="breadcrumb">
            <span>Accueil</span>
            <span class="bc-sep">›</span>
            <span class="bc-active">{{ currentPageTitle }}</span>
          </div>
        </div>
        <div class="topbar-right">
          <div class="search-wrap">
            <svg class="search-icon" width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="7" r="5"/><path d="M11 11l3 3" stroke-linecap="round"/></svg>
            <input v-model="searchQuery" class="search-input" placeholder="Rechercher..." />
          </div>
          <div class="topbar-actions">
            <button class="action-btn notif-btn" @click="showToast('Pas de nouvelles notifications', 'success')" title="Notifications">
              <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1a5 5 0 015 5c0 3.5 1.5 5 1.5 5h-13S3 9.5 3 6a5 5 0 015-5z"/><path d="M6.5 13a1.5 1.5 0 003 0" stroke-linecap="round"/></svg>
              <span class="notif-dot"></span>
            </button>
            <div class="topbar-avatar" @click="showProfile = true" title="Profil">{{ userInitial }}</div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="content">

        <!-- Dashboard Section -->
        <div v-if="activeNav === 'dashboard'">
        <!-- Hero Banner -->
        <div class="hero-banner">
          <div class="hero-text">
            <div class="hero-date">{{ todayDate }}</div>
            <h2 class="hero-title">Bonjour, {{ user.name }} 👋</h2>
            <p class="hero-sub">Voici un aperçu de vos performances et activités du jour.</p>
          </div>
          <div class="hero-stats">
            <div class="hero-kpi">
              <span class="hero-kpi-val">€430K</span>
              <span class="hero-kpi-lbl">Revenu total</span>
            </div>
            <div class="hero-kpi-sep"></div>
            <div class="hero-kpi">
              <span class="hero-kpi-val">94%</span>
              <span class="hero-kpi-lbl">Taux occupation</span>
            </div>
            <div class="hero-kpi-sep"></div>
            <div class="hero-kpi">
              <span class="hero-kpi-val">4.9★</span>
              <span class="hero-kpi-lbl">Note moyenne</span>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
          <div
            v-for="(stat, i) in stats"
            :key="stat.key"
            class="stat-card"
            :class="stat.color"
            :style="{ animationDelay: i * 90 + 'ms' }"
            @click="setActiveNav(stat.navKey)"
          >
            <div class="stat-top">
              <div class="stat-icon-box" v-html="stat.icon"></div>
              <span class="stat-badge" v-if="stat.trend">{{ stat.trend }}</span>
            </div>
            <div class="stat-num">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
            <div class="stat-sub">{{ stat.sub }}</div>
            <div class="stat-footer">
              <span>Accéder</span>
              <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"><path d="M2 7h10M8 3l4 4-4 4"/></svg>
            </div>
          </div>
        </div>

        <!-- Dashboard Row -->
        <div class="dashboard-row">

          <!-- Chart Panel -->
          <article class="panel chart-panel">
            <div class="panel-header">
              <div>
                <h3 class="panel-title">Évolution du rendement</h3>
                <p class="panel-sub">Revenus mensuels sur 6 mois (€)</p>
              </div>
              <div class="chart-legend">
                <span class="legend-dot purple"></span>
                <span class="legend-txt">Revenus 2024–2025</span>
              </div>
            </div>

            <!-- SVG Curve Chart -->
            <div class="chart-area">
              <svg class="curve-svg" viewBox="0 0 560 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                  <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#6366f1" stop-opacity="0.22"/>
                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                  </linearGradient>
                </defs>
                <!-- Grid lines -->
                <line x1="0" y1="40"  x2="560" y2="40"  stroke="#e2e8f0" stroke-width="1"/>
                <line x1="0" y1="80"  x2="560" y2="80"  stroke="#e2e8f0" stroke-width="1"/>
                <line x1="0" y1="120" x2="560" y2="120" stroke="#e2e8f0" stroke-width="1"/>
                <line x1="0" y1="160" x2="560" y2="160" stroke="#e2e8f0" stroke-width="1"/>
                <!-- Area fill -->
                <path :d="areaPath" fill="url(#areaGrad)"/>
                <!-- Curve line -->
                <path :d="curvePath" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Data points -->
                <circle v-for="pt in chartPoints" :key="pt.x" :cx="pt.x" :cy="pt.y" r="4" fill="white" stroke="#6366f1" stroke-width="2"/>
              </svg>
              <!-- X labels -->
              <div class="chart-xlabels">
                <span v-for="pt in chartPoints" :key="pt.month">{{ pt.month }}</span>
              </div>
              <!-- Y labels -->
              <div class="chart-ylabels">
                <span>€90K</span>
                <span>€70K</span>
                <span>€50K</span>
                <span>€30K</span>
              </div>
            </div>

            <div class="chart-metrics">
              <div class="chart-metric">
                <span class="cm-label">Pic mensuel</span>
                <strong class="cm-val">€86 400 <span class="cm-tag green">Avr</span></strong>
              </div>
              <div class="chart-metric">
                <span class="cm-label">Moyenne</span>
                <strong class="cm-val">€71 600</strong>
              </div>
              <div class="chart-metric">
                <span class="cm-label">Croissance</span>
                <strong class="cm-val green">+12.4%</strong>
              </div>
              <div class="chart-metric">
                <span class="cm-label">Projection Mai</span>
                <strong class="cm-val">€91 000</strong>
              </div>
            </div>
          </article>

          <!-- Actions Panel -->
          <article class="panel actions-panel">
            <div class="panel-header">
              <div>
                <h3 class="panel-title">Actions prioritaires</h3>
                <p class="panel-sub">Tâches nécessitant votre attention</p>
              </div>
              <span class="urgent-badge">{{ priorityActions.length }} urgentes</span>
            </div>
            <ul class="action-list">
              <li v-for="item in priorityActions" :key="item.title" class="action-item">
                <div class="action-icon-wrap" :class="item.color">
                  <span v-html="item.icon"></span>
                </div>
                <div class="action-info">
                  <span class="action-title">{{ item.title }}</span>
                  <span class="action-meta">{{ item.meta }}</span>
                </div>
                <button class="action-btn-sm" @click="triggerAction(item.title)">Traiter →</button>
              </li>
            </ul>

            <!-- Recent Activity -->
            <div class="recent-header">
              <span class="panel-title" style="font-size:13px;">Activité récente</span>
            </div>
            <ul class="recent-list">
              <li v-for="act in recentActivity" :key="act.id" class="recent-item">
                <div class="recent-dot" :class="act.color"></div>
                <div class="recent-info">
                  <span class="recent-title">{{ act.title }}</span>
                  <span class="recent-time">{{ act.time }}</span>
                </div>
              </li>
            </ul>
          </article>
        </div>
        </div>

        <!-- Villas Section -->
        <div v-if="activeNav === 'villas'" class="villas-section">
          <div class="villas-header">
            <div>
              <h3 class="villas-title">Mes Villas</h3>
              <p class="villas-sub">Gestion complète de votre portefeuille immobilier</p>
            </div>
            <button class="btn-add-villa" @click="showAddForm = !showAddForm">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 2v12M2 8h12"/></svg>
              Ajouter une villa
            </button>
          </div>

          <!-- Add Form -->
          <div v-if="showAddForm" class="form-card">
            <div class="form-header">
              <h4>{{ editingVillaId ? 'Modifier la villa' : 'Nouvelle villa' }}</h4>
              <button class="form-close" @click="showAddForm = false">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 3l10 10M13 3L3 13"/></svg>
              </button>
            </div>
            <form @submit.prevent="addVilla" class="villa-form">
              <div class="form-row">
                <div class="form-group">
                  <label>Nom de la villa *</label>
                  <input v-model="newVilla.name" type="text" placeholder="Villa Jasmine" />
                </div>
                <div class="form-group">
                  <label>Localisation *</label>
                  <input v-model="newVilla.location" type="text" placeholder="Djerba - Plage" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Prix par nuit (€) *</label>
                  <input v-model="newVilla.price" type="number" placeholder="120" />
                </div>
                <div class="form-group">
                  <label>Nuits minimum</label>
                  <input v-model="newVilla.minNights" type="number" placeholder="1" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Chambres *</label>
                  <input v-model="newVilla.rooms" type="number" placeholder="3" />
                </div>
                <div class="form-group">
                  <label>Lits *</label>
                  <input v-model="newVilla.beds" type="number" placeholder="6" />
                </div>
                <div class="form-group">
                  <label>Hôtes max</label>
                  <input v-model="newVilla.maxGuests" type="number" placeholder="8" />
                </div>
              </div>

              <div class="form-group">
                <label>Image de la villa</label>
                <div class="image-upload-wrap">
                  <label class="image-upload-btn">
                    <input type="file" accept="image/*" class="image-input" @change="handleVillaImageChange" />
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M8 2v12M2 8h12"/></svg>
                    Choisir une image
                  </label>
                  <span class="image-upload-hint">PNG, JPG ou WEBP. Une image claire améliore la mise en avant de la villa.</span>
                </div>
                <div v-if="villaImagePreview" class="image-preview">
                  <img :src="villaImagePreview" alt="Aperçu de la villa" />
                </div>
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea v-model="newVilla.description" placeholder="Décrivez votre villa..." rows="4"></textarea>
              </div>

              <div class="form-group">
                <label>Équipements</label>
                <div class="amenities-grid">
                  <label class="amenity-check">
                    <input type="checkbox" v-model="newVilla.amenities" value="WiFi" />
                    <span>WiFi</span>
                  </label>
                  <label class="amenity-check">
                    <input type="checkbox" v-model="newVilla.amenities" value="Piscine" />
                    <span>Piscine</span>
                  </label>
                  <label class="amenity-check">
                    <input type="checkbox" v-model="newVilla.amenities" value="Climatisation" />
                    <span>Climatisation</span>
                  </label>
                  <label class="amenity-check">
                    <input type="checkbox" v-model="newVilla.amenities" value="Cuisine" />
                    <span>Cuisine</span>
                  </label>
                  <label class="amenity-check">
                    <input type="checkbox" v-model="newVilla.amenities" value="Parking" />
                    <span>Parking</span>
                  </label>
                  <label class="amenity-check">
                    <input type="checkbox" v-model="newVilla.amenities" value="Jardin" />
                    <span>Jardin</span>
                  </label>
                </div>
              </div>

              <div class="form-actions">
                <button type="button" class="btn-cancel" @click="showAddForm = false">Annuler</button>
                <button type="submit" class="btn-save">{{ editingVillaId ? 'Enregistrer les modifications' : 'Ajouter la villa' }}</button>
              </div>
            </form>
          </div>

          <div v-if="userVillas.length === 0 && !loadingVillas" class="no-villas">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1H4a1 1 0 01-1-1V10.5z"/><path d="M9 12h6M9 16h6"/></svg>
            <p>Aucune villa pour le moment</p>
            <button class="btn-add-first" @click="showAddForm = true">Ajouter une villa</button>
          </div>

          <div v-if="loadingVillas" class="loading-state">
            <div class="spinner"></div>
            <p>Chargement des villas...</p>
          </div>

          <div v-if="!loadingVillas && userVillas.length > 0" class="villas-table-wrap">
            <table class="villas-table">
              <thead>
                <tr>
                  <th>Villa</th>
                  <th>Localisation</th>
                  <th>Adresse</th>
                  <th>Prix / Nuit</th>
                  <th>Capacité</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="villa in userVillas" :key="villa.id">
                  <td>
                    <div class="tv-title">{{ villa.title }}</div>
                    <div class="tv-sub">{{ villa.reviews_count || 0 }} avis · {{ villa.reservations_count || 0 }} réservations · ID #{{ villa.id }}</div>
                  </td>
                  <td>{{ villa.city }}, {{ villa.country }}</td>
                  <td class="tv-address">{{ villa.address || '-' }}</td>
                  <td class="tv-price">€{{ villa.price_per_night }}</td>
                  <td>{{ villa.max_guests || 0 }} pers. · {{ villa.bedrooms || 0 }} ch.</td>
                  <td>
                    <span class="table-status" :class="villa.status || 'pending'">{{ villa.status === 'approved' ? 'Approuvée' : villa.status === 'pending' ? 'En attente' : 'Rejetée' }}</span>
                  </td>
                  <td>
                    <div class="table-actions">
                      <button type="button" class="btn-table neutral" @click.stop.prevent="openEditVilla(villa)">Modifier</button>
                      <button type="button" class="btn-table info" @click.stop.prevent="openVillaDetails(villa)">Voir détails</button>
                      <button type="button" class="btn-table danger" @click.stop.prevent="deleteVilla(villa.id)">Supprimer</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <Teleport to="body">
            <div v-if="showVillaDetailsModal" class="modal-overlay" @click.self="closeVillaDetailsModal">
              <div class="modal image-modal">
                <div class="modal-header">
                  <h3>Détails de {{ selectedVillaForDetails?.title }}</h3>
                  <button class="modal-close" @click="closeVillaDetailsModal">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 3l10 10M13 3L3 13"/></svg>
                  </button>
                </div>
                <div class="modal-body">
                  <img v-if="getVillaMainPhoto(selectedVillaForDetails)" class="villa-modal-image" :src="getVillaMainPhoto(selectedVillaForDetails)" :alt="selectedVillaForDetails?.title || 'Image villa'" />
                  <p v-else style="font-size:14px; color:#64748b; margin-bottom:10px;">Aucune image disponible pour cette villa.</p>
                  <div class="details-grid" v-if="selectedVillaForDetails">
                    <div class="detail-item"><strong>Description:</strong> {{ selectedVillaForDetails.description || 'Aucune description' }}</div>
                    <div class="detail-item"><strong>Adresse:</strong> {{ selectedVillaForDetails.address || '-' }}</div>
                    <div class="detail-item"><strong>Prix / nuit:</strong> €{{ selectedVillaForDetails.price_per_night }}</div>
                    <div class="detail-item"><strong>Capacité:</strong> {{ selectedVillaForDetails.max_guests || 0 }} pers. · {{ selectedVillaForDetails.bedrooms || 0 }} ch. · {{ selectedVillaForDetails.bathrooms || 0 }} sdb</div>
                    <div class="detail-item equipments-line">
                      <strong>Équipements:</strong>
                      <div class="modal-equipments">
                        <span :class="['eq-chip', { on: selectedVillaForDetails.has_wifi }]">WiFi</span>
                        <span :class="['eq-chip', { on: selectedVillaForDetails.has_pool }]">Piscine</span>
                        <span :class="['eq-chip', { on: selectedVillaForDetails.has_air_conditioning }]">Climatisation</span>
                        <span :class="['eq-chip', { on: selectedVillaForDetails.has_garden }]">Jardin</span>
                        <span :class="['eq-chip', { on: selectedVillaForDetails.has_garage }]">Garage</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Teleport>
        </div>

      </main>
    </div>

    <!-- ===== PROFILE MODAL ===== -->
    <div v-if="showProfile" class="modal-overlay" @click.self="showProfile = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Mon Profil</h3>
          <button class="modal-close" @click="showProfile = false">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 3l10 10M13 3L3 13"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="profile-avatar-big">{{ userInitial }}</div>
          <div class="profile-name">{{ user.name }}</div>
          <div class="profile-role">Propriétaire · Gestionnaire</div>
          <div class="profile-fields">
            <div class="profile-field">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#6366f1" stroke-width="1.5"><path d="M2 4l6 5 6-5M2 4h12v9H2V4z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span>{{ user.email }}</span>
            </div>
            <div class="profile-field">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#6366f1" stroke-width="1.5"><path d="M2 3a1 1 0 011-1h2.5l1 3-1.5 1a9 9 0 004 4l1-1.5 3 1v2.5a1 1 0 01-1 1A13 13 0 012 3z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span>+216 54 000 000</span>
            </div>
            <div class="profile-field">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#6366f1" stroke-width="1.5"><path d="M8 1a4 4 0 014 4c0 3-4 8-4 8S4 8 4 5a4 4 0 014-4z"/><circle cx="8" cy="5" r="1.5"/></svg>
              <span>Djerba, Tunisie</span>
            </div>
          </div>
          <div class="profile-stats">
            <div class="ps-item">
              <strong>2</strong><span>Villas</span>
            </div>
            <div class="ps-item">
              <strong>3</strong><span>Réservations</span>
            </div>
            <div class="ps-item">
              <strong>4.9★</strong><span>Note</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="showProfile = false">Fermer</button>
          <button class="btn-primary" @click="showToast('Profil modifié', 'success'); showProfile = false">Modifier le profil</button>
        </div>
      </div>
    </div>

    <!-- ===== LOGOUT CONFIRM ===== -->
    <div v-if="showLogout" class="modal-overlay" @click.self="showLogout = false">
      <div class="modal modal-sm">
        <div class="modal-header">
          <h3>Déconnexion</h3>
          <button class="modal-close" @click="showLogout = false">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 3l10 10M13 3L3 13"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <p style="font-size:14px; color:#64748b; line-height:1.6;">Êtes-vous sûr de vouloir vous déconnecter de <strong style="color:#0f172a">VillaHost</strong> ?</p>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="showLogout = false">Annuler</button>
          <button class="btn-danger" @click="confirmLogout">Se déconnecter</button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <transition name="toast">
      <div v-if="toast.show" class="toast" :class="toast.type">
        <svg v-if="toast.type === 'success'" width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="7"/><path d="M5 8l2 2 4-4"/></svg>
        <svg v-else width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="7"/><path d="M8 5v3M8 11v1"/></svg>
        {{ toast.message }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { clearAuthSession, getAuthToken } from '../utils/auth'

const sidebarCollapsed = ref(false)
const activeNav = ref('dashboard')
const searchQuery = ref('')
const showProfile = ref(false)
const showLogout = ref(false)
const toast = ref({ show: false, message: '', type: 'success' })
const showAddForm = ref(false)
const loadingVillas = ref(true)
const villaImageFile = ref(null)
const villaImagePreview = ref('')
const showVillaDetailsModal = ref(false)
const selectedVillaForDetails = ref(null)
const editingVillaId = ref(null)
const router = useRouter()

// Villa data
const userVillas = ref([])

const newVilla = ref({
  name: '',
  location: '',
  price: '',
  rooms: '',
  beds: '',
  description: '',
  amenities: [],
  maxGuests: '',
  minNights: 1
})

const API_BASE = 'http://127.0.0.1:8000/api'

function resetVillaForm() {
  newVilla.value = { name: '', location: '', price: '', rooms: '', beds: '', description: '', amenities: [], maxGuests: '', minNights: 1 }
  editingVillaId.value = null
  villaImageFile.value = null
  if (villaImagePreview.value) {
    URL.revokeObjectURL(villaImagePreview.value)
  }
  villaImagePreview.value = ''
}

function handleVillaImageChange(event) {
  const file = event.target.files?.[0]
  if (!file) {
    villaImageFile.value = null
    return
  }
  villaImageFile.value = file
  if (villaImagePreview.value) {
    URL.revokeObjectURL(villaImagePreview.value)
  }
  villaImagePreview.value = URL.createObjectURL(file)
}

/* ─── API Functions ─── */

async function fetchMyVillas() {
  try {
    const token = getAuthToken()
    if (!token) {
      loadingVillas.value = false
      showToast('Vous devez vous reconnecter pour voir vos villas.', 'error')
      clearAuthSession()
      await router.push({ name: 'auth' })
      return
    }

    loadingVillas.value = true
    const response = await fetch(`${API_BASE}/my-villas`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    if (response.ok) {
      const data = await response.json()
      userVillas.value = Array.isArray(data.data) ? data.data : data.data ? [data.data] : []
    } else if (response.status === 401) {
      showToast('Session expirée. Reconnectez-vous pour voir vos villas.', 'error')
      userVillas.value = []
      clearAuthSession()
      await router.push({ name: 'auth' })
    } else {
      const errorText = await response.text()
      showToast(errorText || 'Erreur lors de la récupération des villas', 'error')
    }
  } catch (error) {
    console.error('Erreur API:', error)
    showToast('Impossible de charger les villas', 'error')
  } finally {
    loadingVillas.value = false
  }
}

async function createVillaAPI(villaData) {
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE}/villas`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        title: villaData.name,
        description: villaData.description || 'Villa élégante avec une vue agréable, un cadre soigné, des espaces confortables et une expérience haut de gamme pour les voyageurs.',
        address: villaData.location,
        city: villaData.location.split('-')[0] || 'Djerba',
        country: 'Tunisie',
        price_per_night: parseInt(villaData.price),
        price: parseInt(villaData.price),
        max_guests: parseInt(villaData.maxGuests) || 8,
        bedrooms: parseInt(villaData.rooms),
        bathrooms: Math.ceil(parseInt(villaData.rooms) / 2),
        min_nights: parseInt(villaData.minNights) || 1,
        listing_type: 'location',
        cancellation_policy: 'moderate',
        has_wifi: villaData.amenities.includes('WiFi'),
        has_pool: villaData.amenities.includes('Piscine'),
        has_air_conditioning: villaData.amenities.includes('Climatisation'),
        has_garden: villaData.amenities.includes('Jardin'),
        has_garage: villaData.amenities.includes('Parking')
      })
    })
    if (response.ok) {
      const data = await response.json()
      if (villaImageFile.value && data.villa?.id) {
        const formData = new FormData()
        formData.append('photo', villaImageFile.value)
        await fetch(`${API_BASE}/villas/${data.villa.id}/photos`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          },
          body: formData
        })
      }
      await fetchMyVillas()
      return data
    } else {
      const errorText = await response.text()
      let message = 'Erreur lors de la création'
      try {
        const parsed = JSON.parse(errorText)
        message = parsed.message || parsed.error || message
      } catch {
        if (errorText) message = errorText
      }
      showToast(message, 'error')
      return null
    }
  } catch (error) {
    console.error('Erreur API création:', error)
    showToast('Impossible de créer la villa', 'error')
    return null
  }
}

async function updateVillaAPI(id, villaData) {
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE}/villas/${id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        title: villaData.name,
        description: villaData.description || 'Villa élégante et confortable.',
        address: villaData.location,
        city: villaData.location.split('-')[0] || 'Djerba',
        country: 'Tunisie',
        price_per_night: parseInt(villaData.price),
        price: parseInt(villaData.price),
        max_guests: parseInt(villaData.maxGuests) || 8,
        bedrooms: parseInt(villaData.rooms),
        bathrooms: Math.ceil(parseInt(villaData.rooms) / 2),
        min_nights: parseInt(villaData.minNights) || 1,
        listing_type: 'location',
        cancellation_policy: 'moderate',
        has_wifi: villaData.amenities.includes('WiFi'),
        has_pool: villaData.amenities.includes('Piscine'),
        has_air_conditioning: villaData.amenities.includes('Climatisation'),
        has_garden: villaData.amenities.includes('Jardin'),
        has_garage: villaData.amenities.includes('Parking')
      })
    })

    if (response.ok) {
      await fetchMyVillas()
      return true
    }

    const errorText = await response.text()
    showToast(errorText || 'Erreur lors de la mise à jour', 'error')
    return false
  } catch (error) {
    console.error('Erreur API update:', error)
    showToast('Impossible de modifier la villa', 'error')
    return false
  }
}

async function deleteVillaAPI(id) {
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE}/villas/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    if (response.ok) {
      userVillas.value = userVillas.value.filter(v => v.id !== id)
      showToast('Villa supprimée avec succès', 'success')
    } else {
      const errorText = await response.text()
      showToast(errorText || 'Erreur lors de la suppression', 'error')
    }
  } catch (error) {
    console.error('Erreur API suppression:', error)
    showToast('Impossible de supprimer la villa', 'error')
  }
}

/* ─── End API Functions ─── */

const user = ref({ name: 'Diallo', email: 'diallo@gmail.com' })
const userInitial = computed(() => user.value.name.charAt(0).toUpperCase())

const todayDate = computed(() => {
  return new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
})

const navItems = [
  {
    key: 'dashboard', label: 'Vue d\'ensemble',
    icon: `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>`
  },
  {
    key: 'villas', label: 'Gérer villas',
    icon: `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 7L8 1l7 6v7a1 1 0 01-1 1H2a1 1 0 01-1-1V7z"/><path d="M5 15V9h6v6"/></svg>`
  },
  {
    key: 'reservations', label: 'Réservations', badge: '3',
    icon: `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="14" height="12" rx="1"/><path d="M1 7h14M5 1v4M11 1v4"/></svg>`
  },
  {
    key: 'reclamations', label: 'Réclamations',
    icon: `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="7"/><path d="M8 5v3M8 11v0.5" stroke-linecap="round"/></svg>`
  },
  {
    key: 'messages', label: 'Messages', badge: '5',
    icon: `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H2a1 1 0 00-1 1v8a1 1 0 001 1h4l2 2 2-2h4a1 1 0 001-1V3a1 1 0 00-1-1z"/><path d="M5 6h6M5 9h4"/></svg>`
  },
]

const pageTitles = {
  dashboard: 'Tableau de bord',
  villas: 'Gérer villas',
  reservations: 'Réservations',
  reclamations: 'Réclamations',
  messages: 'Messages',
}
const currentPageTitle = computed(() => pageTitles[activeNav.value] || 'Tableau de bord')

const stats = ref([
  {
    navKey: 'villas', label: 'Gérer villas', value: '2', sub: 'Villas actives', color: 'purple', trend: '+1 ce mois',
    icon: `<svg width="36" height="36" viewBox="0 0 36 36" fill="none">
      <path d="M3 17L18 5l15 12v14a1 1 0 01-1 1H4a1 1 0 01-1-1V17z" fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"/>
      <rect x="11" y="22" width="6" height="10" rx="1" fill="rgba(255,255,255,0.9)"/>
      <rect x="19" y="19" width="8" height="7" rx="1" fill="rgba(255,255,255,0.6)"/>
      <circle cx="26" cy="10" r="5" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.7)" stroke-width="1.2"/>
      <path d="M23.5 10h5M26 7.5v5" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
    </svg>`
  },
  {
    navKey: 'reservations', label: 'Gérer Réservations', value: '3', sub: 'En attente de validation', color: 'violet', trend: '+3 ce mois',
    icon: `<svg width="36" height="36" viewBox="0 0 36 36" fill="none">
      <rect x="3" y="8" width="30" height="25" rx="2" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
      <path d="M3 15h30" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
      <path d="M11 4v8M25 4v8" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
      <rect x="8" y="20" width="5" height="4" rx="0.8" fill="rgba(255,255,255,0.9)"/>
      <rect x="15.5" y="20" width="5" height="4" rx="0.8" fill="rgba(255,255,255,0.6)"/>
      <rect x="23" y="20" width="5" height="4" rx="0.8" fill="rgba(255,255,255,0.4)"/>
      <rect x="8" y="27" width="5" height="3" rx="0.8" fill="rgba(255,255,255,0.5)"/>
      <rect x="15.5" y="27" width="5" height="3" rx="0.8" fill="rgba(255,255,255,0.7)"/>
    </svg>`
  },
  {
    navKey: 'reclamations', label: 'Gérer Réclamations', value: '0', sub: 'Aucun dossier critique', color: 'red', trend: null,
    icon: `<svg width="36" height="36" viewBox="0 0 36 36" fill="none">
      <path d="M18 4L34 32H2L18 4z" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.7)" stroke-width="1.5" stroke-linejoin="round"/>
      <path d="M18 14v9" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
      <circle cx="18" cy="27" r="1.8" fill="white"/>
    </svg>`
  },
  {
    navKey: 'messages', label: 'Gérer Messages', value: '5', sub: 'Messages non lus', color: 'teal', trend: '+5 ce mois',
    icon: `<svg width="36" height="36" viewBox="0 0 36 36" fill="none">
      <rect x="3" y="8" width="26" height="18" rx="2" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
      <path d="M3 14l13 8 13-8" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"/>
      <circle cx="29" cy="27" r="6" fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"/>
      <path d="M26.5 27h5M29 24.5v5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
    </svg>`
  },
])

// Chart data — monthly revenue in €K
const rawData = [
  { month: 'Nov', val: 42000 },
  { month: 'Déc', val: 55000 },
  { month: 'Jan', val: 61000 },
  { month: 'Fév', val: 53000 },
  { month: 'Mar', val: 68000 },
  { month: 'Avr', val: 86400 },
]

const W = 560, H = 200, PAD = 20
const maxVal = Math.max(...rawData.map(d => d.val))

const chartPoints = computed(() => {
  return rawData.map((d, i) => ({
    month: d.month,
    val: d.val,
    x: PAD + (i / (rawData.length - 1)) * (W - PAD * 2),
    y: PAD + (1 - d.val / maxVal) * (H - PAD * 2),
  }))
})

function cubicBezierPath(pts) {
  if (pts.length < 2) return ''
  let d = `M ${pts[0].x} ${pts[0].y}`
  for (let i = 0; i < pts.length - 1; i++) {
    const cp1x = pts[i].x + (pts[i + 1].x - pts[i].x) * 0.5
    const cp1y = pts[i].y
    const cp2x = pts[i].x + (pts[i + 1].x - pts[i].x) * 0.5
    const cp2y = pts[i + 1].y
    d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${pts[i + 1].x} ${pts[i + 1].y}`
  }
  return d
}

const curvePath = computed(() => cubicBezierPath(chartPoints.value))

const areaPath = computed(() => {
  const pts = chartPoints.value
  if (!pts.length) return ''
  const curve = cubicBezierPath(pts)
  return `${curve} L ${pts[pts.length - 1].x} ${H} L ${pts[0].x} ${H} Z`
})

const priorityActions = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  const list = [
    {
      title: 'Réservations à valider', meta: '3 demandes en attente · Urgent', color: 'violet',
      icon: `<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="14" height="12" rx="1"/><path d="M1 7h14M5 1v4M11 1v4"/></svg>`
    },
    {
      title: 'Messages non lus', meta: '5 conversations à traiter', color: 'teal',
      icon: `<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H2a1 1 0 00-1 1v8a1 1 0 001 1h4l2 2 2-2h4a1 1 0 001-1V3a1 1 0 00-1-1z"/></svg>`
    },
    {
      title: 'Mettre à jour vos villas', meta: 'Photos & descriptions obsolètes', color: 'amber',
      icon: `<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 7L8 1l7 6v7a1 1 0 01-1 1H2a1 1 0 01-1-1V7z"/></svg>`
    },
  ]
  if (!q) return list
  return list.filter(a => a.title.toLowerCase().includes(q) || a.meta.toLowerCase().includes(q))
})

const recentActivity = ref([
  { id: 1, title: 'Nouvelle réservation — Villa Jasmine', time: 'Il y a 12 min', color: 'green' },
  { id: 2, title: 'Message reçu — Client Ahmed B.', time: 'Il y a 45 min', color: 'blue' },
  { id: 3, title: 'Paiement confirmé — €2 500', time: 'Il y a 2h', color: 'green' },
  { id: 4, title: 'Avis 5★ reçu — Villa Oasis', time: 'Il y a 5h', color: 'amber' },
])

function triggerAction(label) {
  showToast(`"${label}" ouvert`, 'success')
}

function setActiveNav(key) {
  activeNav.value = key

  if (key === 'villas') {
    fetchMyVillas()
  }
}

function formatRating(rating) {
  const numericRating = Number(rating)
  return Number.isFinite(numericRating) && numericRating > 0 ? numericRating.toFixed(1) : 'N/A'
}

function getVillaMainPhoto(villa) {
  if (!villa?.photos?.length) return ''
  return `http://127.0.0.1:8000/storage/${villa.photos[0].path}`
}

function getVillaEquipments(villa) {
  if (!villa) return 'Aucun équipement renseigné'

  const items = [
    villa.has_wifi ? 'WiFi' : null,
    villa.has_pool ? 'Piscine' : null,
    villa.has_air_conditioning ? 'Climatisation' : null,
    villa.has_garden ? 'Jardin' : null,
    villa.has_garage ? 'Garage' : null,
  ].filter(Boolean)

  return items.length ? items.join(' · ') : 'Aucun équipement renseigné'
}

function openVillaDetails(villa) {
  selectedVillaForDetails.value = villa
  showVillaDetailsModal.value = true
}

function closeVillaDetailsModal() {
  showVillaDetailsModal.value = false
  selectedVillaForDetails.value = null
}

function openEditVilla(villa) {
  editingVillaId.value = villa.id
  newVilla.value = {
    name: villa.title || '',
    location: villa.address || `${villa.city || ''}-${villa.country || ''}`,
    price: villa.price_per_night || villa.price || '',
    rooms: villa.bedrooms || '',
    beds: villa.bedrooms || '',
    description: villa.description || '',
    amenities: [
      villa.has_wifi ? 'WiFi' : null,
      villa.has_pool ? 'Piscine' : null,
      villa.has_air_conditioning ? 'Climatisation' : null,
      villa.has_garden ? 'Jardin' : null,
      villa.has_garage ? 'Parking' : null,
    ].filter(Boolean),
    maxGuests: villa.max_guests || '',
    minNights: villa.min_nights || 1,
  }
  showAddForm.value = true
  nextTick(() => {
    const target = document.querySelector('.form-card')
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' })
  })
}

function handleLogout() {
  showLogout.value = true
}

function confirmLogout() {
  showLogout.value = false
  showToast('Déconnexion réussie', 'success')
}

function showToast(message, type = 'success') {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 3200)
}

function addVilla() {
  if (!newVilla.value.name || !newVilla.value.location || !newVilla.value.price) {
    showToast('Veuillez remplir tous les champs obligatoires', 'error')
    return
  }

  if (editingVillaId.value) {
    updateVillaAPI(editingVillaId.value, newVilla.value).then((ok) => {
      if (ok) {
        resetVillaForm()
        showAddForm.value = false
        showToast('Villa modifiée avec succès !', 'success')
      }
    })
    return
  }

  createVillaAPI(newVilla.value).then(result => {
    if (result) {
      resetVillaForm()
      showAddForm.value = false
      showToast('Villa ajoutée avec succès !', 'success')
    }
  })
}

function deleteVilla(id) {
  deleteVillaAPI(id)
}

onMounted(() => {
  if (getAuthToken()) {
    fetchMyVillas()
  } else {
    loadingVillas.value = false
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600&family=Syne:wght@600;700;800&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --sb-w: 280px;
  --sb-bg: #0b1024;
  --sb-border: rgba(255,255,255,0.12);
  --primary: #6366f1;
  --primary-d: #4f46e5;
  --surface: #ffffff;
  --bg: #f1f5f9;
  --text: #0f172a;
  --muted: #64748b;
  --border: rgba(15,23,42,0.08);
  --r: 28px;
  --r-sm: 16px;
  --shadow: 0 1px 4px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.05);
  --shadow-lg: 0 12px 32px rgba(0,0,0,0.14);
  --font: 'DM Sans', sans-serif;
  --display: 'Syne', sans-serif;
}

.app-layout {
  display: flex; height: 100vh;
  font-family: var(--font);
  background: var(--bg);
  color: var(--text);
  overflow: hidden;
}

/* ════════════════ SIDEBAR ════════════════ */
.sidebar {
  width: var(--sb-w); min-width: var(--sb-w);
  background: #0d0f1e !important;
  display: flex; flex-direction: column;
  transition: width 0.28s cubic-bezier(.4,0,.2,1), min-width 0.28s cubic-bezier(.4,0,.2,1);
  position: relative; z-index: 20;
  box-shadow: 4px 0 28px rgba(0,0,0,0.45); border-right: 1px solid rgba(255,255,255,0.07);
}
.sidebar.collapsed { width: 68px; min-width: 68px; }

.sidebar-logo {
  display: flex; align-items: center; gap: 10px;
  padding: 22px 16px 18px;
  border-bottom: 1px solid var(--sb-border);
  position: relative; flex-shrink: 0;
}
.logo-icon {
  width: 38px; height: 38px; border-radius: 11px;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; box-shadow: 0 4px 12px rgba(99,102,241,0.4);
}
.logo-text { display: flex; flex-direction: column; overflow: hidden; }
.logo-name {
  color: #fff; font-family: var(--display);
  font-size: 17px; font-weight: 800; white-space: nowrap;
  letter-spacing: -0.3px;
}
.logo-sub { color: rgba(255,255,255,0.35); font-size: 10px; white-space: nowrap; margin-top: 1px; }

.collapse-btn {
  position: absolute; right: -11px; top: 50%; transform: translateY(-50%);
  width: 22px; height: 22px; border-radius: 50%;
  background: #2d2970; border: 1.5px solid rgba(255,255,255,0.12);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background 0.15s; z-index: 5;
}
.collapse-btn:hover { background: #3d3a9e; }

.nav-section-label {
  padding: 14px 18px 6px;
  font-size: 9px; font-weight: 700; letter-spacing: 1.2px;
  text-transform: uppercase; color: rgba(255,255,255,0.40);
}

.sidebar-nav { flex: 1; padding: 4px 10px 0; overflow-y: auto; }
.sidebar-nav::-webkit-scrollbar { width: 0; }

.nav-item {
  display: flex; align-items: center; gap: 11px;
  padding: 10px 12px; border-radius: var(--r-sm);
  cursor: pointer; color: rgba(255,255,255,0.75);
  font-size: 13px; font-weight: 500;
  transition: background 0.15s, color 0.15s;
  margin-bottom: 3px; white-space: nowrap;
  position: relative; user-select: none;
}
.nav-item:hover { background: rgba(255,255,255,0.11); color: rgba(255,255,255,0.92); }
.nav-item.active {
  background: #fff; color: var(--primary-d);
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(99,102,241,0.2);
}
.nav-icon { flex-shrink: 0; display: flex; width: 16px; }
.nav-label { flex: 1; }
.nav-badge {
  background: #6366f1; color: #fff;
  font-size: 10px; font-weight: 700;
  padding: 2px 7px; border-radius: 20px; min-width: 20px; text-align: center;
}
.nav-item.active .nav-badge { background: var(--primary); }

.sidebar-divider { height: 1px; background: var(--sb-border); margin: 8px 10px; }

.sidebar-bottom { padding: 4px 10px; }
.bottom-item { color: rgba(255,255,255,0.65); }
.logout:hover { background: rgba(239,68,68,0.15) !important; color: #fca5a5 !important; }

.sidebar-user {
  padding: 14px 14px 18px;
  border-top: 1px solid var(--sb-border);
  display: flex; align-items: center; gap: 10px; flex-shrink: 0;
}
.user-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, #818cf8, #c084fc);
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 14px; font-weight: 700; flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(129,140,248,0.4);
}
.user-info { display: flex; flex-direction: column; overflow: hidden; }
.user-name { color: #fff; font-size: 13px; font-weight: 600; white-space: nowrap; }
.user-email { color: rgba(255,255,255,0.50); font-size: 11px; white-space: nowrap; margin-top: 1px; }

/* ════════════════ MAIN ════════════════ */
.main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

.topbar {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 12px 26px;
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px; flex-shrink: 0;
  box-shadow: 0 1px 0 var(--border);
}
.topbar-left { display: flex; flex-direction: column; gap: 1px; }
.page-title { font-family: var(--display); font-size: 19px; font-weight: 700; color: var(--text); }
.breadcrumb { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--muted); }
.bc-sep { opacity: 0.5; }
.bc-active { color: var(--primary); font-weight: 500; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.search-wrap { position: relative; }
.search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
.search-input {
  padding: 8px 14px 8px 32px; font-size: 13px;
  border: 1px solid var(--border); border-radius: var(--r-sm);
  background: var(--bg); color: var(--text); width: 210px;
  font-family: var(--font); outline: none; transition: border 0.15s, box-shadow 0.15s;
}
.search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.topbar-actions { display: flex; align-items: center; gap: 8px; }
.notif-btn {
  width: 36px; height: 36px; border-radius: var(--r-sm);
  background: var(--bg); border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: var(--muted); position: relative;
  transition: background 0.15s, color 0.15s;
}
.notif-btn:hover { background: var(--surface); color: var(--text); }
.notif-btn:active { transform: scale(0.95); }
.notif-dot {
  width: 8px; height: 8px; border-radius: 50%; background: #ef4444;
  position: absolute; top: 5px; right: 5px;
  border: 2px solid var(--surface);
}
.topbar-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 13px; font-weight: 700; cursor: pointer;
  box-shadow: 0 2px 8px rgba(99,102,241,0.3);
  transition: transform 0.15s;
}
.topbar-avatar:hover { transform: scale(1.05); }

/* ════════════════ CONTENT ════════════════ */
.content { flex: 1; overflow-y: auto; padding: 22px 26px; }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

/* ── Hero ── */
.hero-banner {
  background: linear-gradient(130deg, #1e1b4b 0%, #2e2b7a 55%, #4c1d95 100%);
  border-radius: 28px;
  padding: 24px 28px;
  margin-bottom: 20px;
  display: flex; align-items: center; justify-content: space-between;
  gap: 20px;
}
.hero-date { color: rgba(255,255,255,0.4); font-size: 11px; text-transform: capitalize; margin-bottom: 5px; }
.hero-title { font-family: var(--display); font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 5px; }
.hero-sub { font-size: 13px; color: rgba(255,255,255,0.65); }
.hero-stats { display: flex; align-items: center; gap: 0; flex-shrink: 0; }
.hero-kpi { display: flex; flex-direction: column; align-items: center; padding: 0 24px; }
.hero-kpi-val { font-family: var(--display); font-size: 22px; font-weight: 800; color: #fff; }
.hero-kpi-lbl { font-size: 10px; color: rgba(255,255,255,0.4); margin-top: 2px; white-space: nowrap; }
.hero-kpi-sep { width: 1px; height: 40px; background: rgba(255,255,255,0.12); }

/* ── Stats ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px; margin-bottom: 20px;
}
.stat-card {
  border-radius: 28px; padding: 18px 16px;
  cursor: pointer; position: relative; overflow: hidden;
  animation: fadeUp 0.5s both;
  transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.2); }
.stat-card.purple { background: linear-gradient(140deg,#4338ca,#6366f1); }
.stat-card.violet { background: linear-gradient(140deg,#6d28d9,#a855f7); }
.stat-card.red    { background: linear-gradient(140deg,#b91c1c,#ef4444); }
.stat-card.teal   { background: linear-gradient(140deg,#0e7490,#06b6d4); }

/* Decorative circle */
.stat-card::after {
  content: ''; position: absolute;
  width: 100px; height: 100px; border-radius: 50%;
  background: rgba(255,255,255,0.06);
  bottom: -30px; right: -20px;
  pointer-events: none;
}

.stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
.stat-icon-box { opacity: 0.95; }
.stat-badge {
  background: rgba(255,255,255,0.18); color: rgba(255,255,255,0.9);
  font-size: 10px; border-radius: 20px; padding: 3px 9px;
  white-space: nowrap; font-weight: 600;
}
.stat-num { color: #fff; font-family: var(--display); font-size: 34px; font-weight: 800; line-height: 1; margin-bottom: 5px; }
.stat-label { color: rgba(255,255,255,0.9); font-size: 12px; font-weight: 600; margin-bottom: 2px; }
.stat-sub { color: rgba(255,255,255,0.65); font-size: 11px; }
.stat-footer {
  display: flex; align-items: center; justify-content: flex-end; gap: 4px;
  margin-top: 12px; color: rgba(255,255,255,0.55); font-size: 11px;
}

/* ── Dashboard Row ── */
.dashboard-row {
  display: grid;
  grid-template-columns: 1.65fr 1fr;
  gap: 16px;
}

.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 28px;
  padding: 20px;
  box-shadow: var(--shadow);
}
.panel-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; }
.panel-title { font-family: var(--display); font-size: 15px; font-weight: 700; color: var(--text); }
.panel-sub { font-size: 12px; color: var(--muted); margin-top: 3px; }

/* ── Chart ── */
.chart-legend { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--muted); }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; }
.legend-dot.purple { background: #6366f1; }

.chart-area { position: relative; margin-bottom: 14px; }
.curve-svg { width: 100%; height: 200px; display: block; }
.chart-xlabels {
  display: flex; justify-content: space-between;
  padding: 6px 20px 0;
  font-size: 11px; color: var(--muted); font-weight: 500;
}
.chart-ylabels {
  position: absolute; left: 0; top: 0; bottom: 0;
  display: flex; flex-direction: column; justify-content: space-between;
  padding: 14px 0 24px;
  font-size: 10px; color: var(--muted);
  pointer-events: none;
}

.chart-metrics {
  display: grid; grid-template-columns: repeat(4, minmax(0,1fr));
  gap: 10px; padding-top: 14px;
  border-top: 1px solid var(--border);
}
.chart-metric { display: flex; flex-direction: column; gap: 3px; }
.cm-label { font-size: 10px; color: var(--muted); }
.cm-val { font-family: var(--display); font-size: 15px; font-weight: 700; color: var(--text); }
.cm-val.green { color: #059669; }
.cm-tag { font-size: 9px; font-weight: 600; padding: 1px 6px; border-radius: 10px; }
.cm-tag.green { background: #d1fae5; color: #065f46; }

/* ── Actions Panel ── */
.urgent-badge {
  background: #fef3c7; color: #92400e;
  font-size: 10px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px; white-space: nowrap;
}

.action-list { list-style: none; display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
.action-item {
  display: flex; align-items: center; gap: 10px;
  background: #f8fafc; border: 1px solid #e2e8f0;
  border-radius: var(--r-sm); padding: 10px 12px;
  transition: box-shadow 0.15s;
}
.action-item:hover { box-shadow: var(--shadow); }
.action-icon-wrap {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.action-icon-wrap.violet { background: #ede9fe; color: #6d28d9; }
.action-icon-wrap.teal   { background: #ccfbf1; color: #0f766e; }
.action-icon-wrap.amber  { background: #fef3c7; color: #92400e; }

.action-info { flex: 1; display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.action-title { font-size: 13px; font-weight: 600; color: var(--text); }
.action-meta { font-size: 11px; color: var(--muted); }
.action-btn-sm {
  background: #eef2ff; color: #4338ca;
  border: 1px solid #c7d2fe; border-radius: var(--r-sm);
  padding: 6px 10px; font-size: 11px; font-weight: 600;
  cursor: pointer; white-space: nowrap; font-family: var(--font);
  transition: filter 0.15s, transform 0.1s;
}
.action-btn-sm:hover { filter: brightness(0.95); }
.action-btn-sm:active { transform: scale(0.96); }

.recent-header { margin-bottom: 10px; padding-top: 4px; }
.recent-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.recent-item { display: flex; align-items: flex-start; gap: 10px; }
.recent-dot {
  width: 8px; height: 8px; border-radius: 50%; margin-top: 4px; flex-shrink: 0;
}
.recent-dot.green  { background: #10b981; }
.recent-dot.blue   { background: #3b82f6; }
.recent-dot.amber  { background: #f59e0b; }
.recent-info { display: flex; flex-direction: column; gap: 2px; }
.recent-title { font-size: 12px; color: var(--text); font-weight: 500; }
.recent-time { font-size: 11px; color: var(--muted); }

/* ── Modal ── */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(15,23,42,0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000;
}
.modal {
  background: var(--surface); border-radius: var(--r);
  width: 420px; max-width: calc(100vw - 32px);
  box-shadow: var(--shadow-lg); animation: scaleIn 0.2s ease;
}
.modal-sm { width: 360px; }
.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 20px 14px; border-bottom: 1px solid var(--border);
}
.modal-header h3 { font-family: var(--display); font-size: 15px; font-weight: 700; }
.modal-close {
  width: 28px; height: 28px; border-radius: 8px;
  background: var(--bg); border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; color: var(--muted);
}
.modal-body { padding: 20px; }
.profile-avatar-big {
  width: 64px; height: 64px; border-radius: 50%;
  background: linear-gradient(135deg, #818cf8, #c084fc);
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 26px; font-weight: 800;
  margin: 0 auto 12px; box-shadow: 0 4px 16px rgba(129,140,248,0.4);
}
.profile-name { font-family: var(--display); font-size: 18px; font-weight: 800; text-align: center; color: var(--text); }
.profile-role { font-size: 12px; color: var(--muted); text-align: center; margin-top: 3px; margin-bottom: 16px; }
.profile-fields { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
.profile-field {
  display: flex; align-items: center; gap: 10px;
  background: var(--bg); border-radius: var(--r-sm);
  padding: 10px 12px; font-size: 13px; color: var(--text);
}
.profile-stats {
  display: flex; justify-content: center; gap: 0;
  border: 1px solid var(--border); border-radius: var(--r-sm); overflow: hidden;
}
.ps-item {
  flex: 1; display: flex; flex-direction: column; align-items: center;
  padding: 12px 8px; border-right: 1px solid var(--border);
}
.ps-item:last-child { border-right: none; }
.ps-item strong { font-family: var(--display); font-size: 18px; font-weight: 800; color: var(--text); }
.ps-item span { font-size: 11px; color: var(--muted); margin-top: 2px; }

.modal-footer {
  display: flex; justify-content: flex-end; gap: 8px;
  padding: 14px 20px 18px; border-top: 1px solid var(--border);
}
.btn-cancel {
  background: var(--bg); color: var(--muted);
  border: 1px solid var(--border); border-radius: var(--r-sm);
  padding: 8px 16px; font-size: 13px; cursor: pointer; font-family: var(--font);
}
.btn-primary {
  display: flex; align-items: center; gap: 6px;
  background: var(--primary); color: #fff; border: none;
  border-radius: var(--r-sm); padding: 8px 16px;
  font-size: 13px; font-weight: 600; cursor: pointer; font-family: var(--font);
  transition: background 0.15s;
}
.btn-primary:hover { background: var(--primary-d); }
.btn-danger {
  background: #dc2626; color: #fff; border: none;
  border-radius: var(--r-sm); padding: 8px 16px;
  font-size: 13px; font-weight: 600; cursor: pointer; font-family: var(--font);
  transition: background 0.15s;
}
.btn-danger:hover { background: #b91c1c; }

/* ── Toast ── */
.toast {
  position: fixed; bottom: 24px; right: 24px;
  background: #1e293b; color: #fff;
  padding: 12px 18px; border-radius: var(--r-sm);
  font-size: 13px; display: flex; align-items: center; gap: 8px;
  box-shadow: var(--shadow-lg); z-index: 999;
}
.toast.success { background: #064e3b; }
.toast.error   { background: #7f1d1d; }
.toast-enter-active, .toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }

/* ── Transitions ── */
.fade-text-enter-active, .fade-text-leave-active { transition: opacity 0.2s; }
.fade-text-enter-from, .fade-text-leave-to { opacity: 0; }

/* ── Animations ── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.94); }
  to   { opacity: 1; transform: scale(1); }
}

/* ── Responsive ── */
@media (max-width: 1100px) {
  .stats-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
  .dashboard-row { grid-template-columns: 1fr; }
  .hero-stats { display: none; }
}
@media (max-width: 640px) {
  .content { padding: 14px; }
  .stats-grid { grid-template-columns: 1fr; }
  .chart-metrics { grid-template-columns: repeat(2, minmax(0,1fr)); }
}

/* ── VILLAS SECTION ── */
.villas-section { animation: fadeUp 0.35s ease; }

.villas-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 20px; margin-bottom: 28px;
}

.villas-title { font-family: var(--display); font-size: 28px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
.villas-sub { font-size: 13px; color: var(--muted); }

.btn-add-villa {
  display: flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); color: #fff; border: none;
  border-radius: var(--r-sm); padding: 10px 18px;
  font-size: 13px; font-weight: 600; cursor: pointer; font-family: var(--font);
  box-shadow: 0 10px 22px rgba(249,115,22,0.28);
  transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
  white-space: nowrap;
}
.btn-add-villa:hover { background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%); transform: translateY(-1px); box-shadow: 0 12px 24px rgba(234,88,12,0.34); }
.btn-add-villa:active { transform: scale(0.98); }

/* ── FORM CARD ── */
.form-card {
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  border: 1px solid rgba(148,163,184,0.18);
  border-radius: var(--r); padding: 24px;
  margin-bottom: 28px; box-shadow: 0 18px 40px rgba(15,23,42,0.10);
}

.form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.form-header h4 { font-family: var(--display); font-size: 18px; font-weight: 700; color: var(--text); }
.form-close { background: #eef2ff; border: 1px solid rgba(99,102,241,0.12); color: var(--primary); cursor: pointer; transition: all 0.15s; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.form-close:hover { color: #fff; background: var(--primary); }

.villa-form { display: flex; flex-direction: column; gap: 18px; }

.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }

.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 12px; font-weight: 600; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; }
.form-group input,
.form-group textarea {
  background: #fff; border: 1px solid rgba(148,163,184,0.24);
  border-radius: var(--r-sm); padding: 12px 14px;
  font-family: var(--font); font-size: 13px; color: var(--text);
  transition: border 0.15s, background 0.15s, box-shadow 0.15s;
}
.form-group input:focus,
.form-group textarea:focus { outline: none; border-color: #818cf8; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,0.16); }

.form-group input::placeholder,
.form-group textarea::placeholder { color: rgba(100,116,139,0.55); }

.amenities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; }

.optional { color: #64748b; font-weight: 400; font-size: 11px; text-transform: lowercase; letter-spacing: 0; }
.amenity-check {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 12px; border: 1px solid rgba(148,163,184,0.16);
  border-radius: var(--r-sm); cursor: pointer; transition: all 0.15s;
  font-size: 13px; color: var(--text); background: #fff;
}
.amenity-check input { cursor: pointer; }
.amenity-check input:checked + span { color: var(--primary); font-weight: 700; }

.image-upload-wrap { display: flex; flex-direction: column; gap: 10px; }
.image-upload-btn {
  display: inline-flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
  color: #fff; border-radius: var(--r-sm); padding: 10px 14px;
  width: fit-content; cursor: pointer; font-size: 13px; font-weight: 700;
  box-shadow: 0 10px 22px rgba(99,102,241,0.22);
  transition: transform 0.15s, box-shadow 0.15s;
}
.image-upload-btn:hover { transform: translateY(-1px); box-shadow: 0 14px 24px rgba(99,102,241,0.30); }
.image-input { display: none; }
.image-upload-hint { font-size: 12px; color: #64748b; }
.image-preview {
  border: 1px solid rgba(148,163,184,0.18);
  border-radius: 20px; overflow: hidden;
  background: #f8fafc;
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
}
.image-preview img { display: block; width: 100%; height: 220px; object-fit: cover; }

.form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px; }
.btn-cancel, .btn-save { border: none; border-radius: var(--r-sm); padding: 11px 18px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: var(--font); transition: all 0.15s; }
.btn-cancel { background: #f8fafc; color: var(--text); border: 1px solid rgba(148,163,184,0.22); }
.btn-cancel:hover { background: #eef2ff; }
.btn-save { background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); color: #fff; box-shadow: 0 10px 24px rgba(34,197,94,0.22); }
.btn-save:hover { filter: brightness(1.05); transform: translateY(-1px); }

/* ── VILLAS GRID ── */
.villas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }

.villa-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--r); overflow: hidden;
  transition: box-shadow 0.3s, transform 0.3s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.villa-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,0.16); transform: translateY(-4px); }

.villa-image {
  position: relative; width: 100%; height: 260px; overflow: hidden; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
}
.villa-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s cubic-bezier(.4,0,.2,1); }
.villa-card:hover .villa-image img { transform: scale(1.08); }

.placeholder-image {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
}

.villa-status {
  position: absolute; top: 12px; right: 12px;
  padding: 6px 12px; border-radius: 20px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  background: #064e3b; color: #ecfdf5;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.villa-status.approved { background: #064e3b; color: #ecfdf5; }
.villa-status.pending { background: #b45309; color: #fffbeb; }
.villa-status.rejected { background: #7f1d1d; color: #fef2f2; }

.villa-info { padding: 18px; }

.villa-name { 
  font-family: var(--display); font-size: 18px; font-weight: 800; 
  color: var(--text); margin-bottom: 6px;
  line-height: 1.2;
  word-wrap: break-word;
}

.villa-location {
  display: flex; align-items: center; gap: 6px;
  font-size: 13px; color: var(--muted); margin-bottom: 14px;
}
.villa-location svg { color: var(--primary); flex-shrink: 0; }

.villa-meta {
  display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 14px;
  padding-bottom: 14px; border-bottom: 1px solid var(--border);
}
.meta-item { 
  display: flex; align-items: center; gap: 6px; 
  font-size: 12px; color: var(--text); font-weight: 500;
}
.meta-item svg { color: var(--primary); flex-shrink: 0; }
.meta-item.price { color: var(--primary); font-weight: 700; }

.villa-stats { 
  display: flex; justify-content: space-around; margin-bottom: 16px;
  padding: 12px 0; border-radius: var(--r-sm);
  background: linear-gradient(135deg, rgba(99,102,241,0.05) 0%, rgba(139,92,246,0.05) 100%);
}
.stat { 
  display: flex; flex-direction: column; align-items: center;
  flex: 1;
}
.stat-val { 
  font-family: var(--display); font-weight: 800; font-size: 16px; 
  color: var(--primary);
}
.stat-lbl { font-size: 10px; color: var(--muted); margin-top: 3px; text-transform: uppercase; letter-spacing: 0.3px; }

.villa-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }
.btn-action {
  display: flex; align-items: center; justify-content: center; gap: 5px;
  background: var(--bg); border: 1px solid var(--border);
  border-radius: var(--r-sm); padding: 9px 8px;
  font-size: 11px; font-weight: 700; color: var(--text);
  cursor: pointer; transition: all 0.2s; font-family: var(--font);
  text-transform: uppercase; letter-spacing: 0.3px;
}
.btn-action:hover { 
  background: var(--primary); color: #fff; border-color: var(--primary);
  transform: translateY(-1px);
}
.btn-action.delete:hover { background: #dc2626; border-color: #dc2626; color: #fff; }

.loading-state {
  grid-column: 1 / -1;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 80px 20px;
  background: linear-gradient(135deg, rgba(99,102,241,0.05) 0%, rgba(139,92,246,0.05) 100%);
  border: 2px dashed var(--border); border-radius: var(--r);
}
.spinner {
  width: 40px; height: 40px; border: 3px solid var(--border);
  border-top-color: var(--primary); border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }

.no-villas {
  grid-column: 1 / -1;
  text-align: center; padding: 60px 20px;
  background: linear-gradient(135deg, rgba(99,102,241,0.05) 0%, rgba(139,92,246,0.05) 100%);
  border: 2px dashed var(--border); border-radius: var(--r);
}
.no-villas svg { color: var(--muted); margin-bottom: 12px; opacity: 0.5; }
.no-villas p { font-size: 14px; color: var(--muted); margin-bottom: 14px; }
.btn-add-first {
  background: var(--primary); color: #fff; border: none;
  border-radius: var(--r-sm); padding: 10px 20px;
  font-size: 13px; font-weight: 600; cursor: pointer; font-family: var(--font);
  transition: background 0.15s;
}
.btn-add-first:hover { background: var(--primary-d); }

/* ── VILLAS TABLE ── */
.villas-table-wrap {
  background: #fff;
  border: 1px solid rgba(148,163,184,0.28);
  border-radius: var(--r);
  overflow: hidden;
  box-shadow: 0 16px 34px rgba(15,23,42,0.10);
}

.villas-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.villas-table thead {
  background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.villas-table th {
  text-align: left;
  padding: 13px 14px;
  border-bottom: 1px solid rgba(148,163,184,0.2);
  color: #334155;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.45px;
}

.villas-table td {
  padding: 14px;
  border-bottom: 1px solid rgba(148,163,184,0.12);
  color: #0f172a;
  vertical-align: middle;
}

.villas-table tbody tr:hover {
  background: #f8faff;
}

.villas-table tbody tr:nth-child(even) {
  background: #fcfdff;
}

.tv-title {
  font-weight: 700;
  margin-bottom: 2px;
}

.tv-sub {
  font-size: 11px;
  color: #64748b;
}

.tv-address {
  max-width: 240px;
  color: #334155;
}

.tv-price {
  font-weight: 700;
  color: var(--primary-d);
}

.table-status {
  display: inline-block;
  border-radius: 999px;
  padding: 4px 10px;
  font-size: 11px;
  font-weight: 700;
}

.table-status.approved { background: #dcfce7; color: #166534; }
.table-status.pending { background: #fef3c7; color: #92400e; }
.table-status.rejected { background: #fee2e2; color: #991b1b; }

.table-actions {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  position: relative;
  z-index: 2;
}

.btn-table {
  border: 1px solid transparent;
  border-radius: 10px;
  padding: 7px 10px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  pointer-events: auto;
  position: relative;
  z-index: 3;
}

.btn-table.neutral {
  background: #eef2ff;
  color: #4338ca;
  border-color: #c7d2fe;
}

.btn-table.neutral:hover {
  background: #e0e7ff;
}

.btn-table.info {
  background: #dbeafe;
  color: #1d4ed8;
}

.btn-table.info:hover {
  background: #bfdbfe;
}

.btn-table.danger {
  background: #fee2e2;
  color: #b91c1c;
}

.btn-table.danger:hover {
  background: #fecaca;
}

.eq-chip {
  font-size: 10px;
  font-weight: 700;
  padding: 3px 7px;
  border-radius: 999px;
  background: #f1f5f9;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.eq-chip.on {
  background: #dcfce7;
  color: #166534;
  border-color: #bbf7d0;
}

.image-modal {
  max-width: 760px;
}

.villa-modal-image {
  width: 100%;
  max-height: 70vh;
  object-fit: cover;
  border-radius: 14px;
  border: 1px solid rgba(148,163,184,0.2);
  margin-bottom: 14px;
}

.details-grid {
  display: grid;
  gap: 10px;
  background: #f8fafc;
  border: 1px solid rgba(148,163,184,0.2);
  border-radius: 12px;
  padding: 12px;
}

.detail-item {
  font-size: 13px;
  color: #334155;
  line-height: 1.45;
}

.equipments-line {
  display: grid;
  gap: 8px;
}

.modal-equipments {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

</style>