<template>
  <div class="hero-video">
    <video
      class="hero-video__media"
      autoplay
      muted
      loop
      playsinline
      preload="metadata"
    >
      <source src="/assets/videos/hero.mp4" type="video/mp4" />
    </video>

    <div class="hero-video__overlay">
      <h1 class="hero-video__brand" aria-label="Trueleey.com">
        <span
          v-for="(char, i) in brandChars"
          :key="i"
          class="hero-video__char"
          :class="{ 'is-accent': char.accent }"
          :style="{ '--i': i }"
        >{{ char.value }}</span>
      </h1>
      <h2 class="hero-video__title">Verify products. Run your supply chain.</h2>
      <p class="hero-video__subtitle">
        Authenticate goods, manage manufacturers to retailers, and make payments — all in one place.
      </p>
      <div>
        <router-link to="/login" class="btn theme-btn-1">Get Started</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'app-slider',
  data() {
    return {
      brandChars: [
        { value: 'T', accent: false },
        { value: 'r', accent: false },
        { value: 'u', accent: false },
        { value: 'e', accent: false },
        { value: 'l', accent: false },
        { value: 'e', accent: false },
        { value: 'e', accent: false },
        { value: 'y', accent: false },
        { value: '.', accent: true },
        { value: 'c', accent: true },
        { value: 'o', accent: true },
        { value: 'm', accent: true },
      ],
    }
  },
}
</script>

<style scoped>
.hero-video {
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  min-height: 480px;
  background-color: #1A1D23;
}

.hero-video__media {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-video__overlay {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 480px;
  padding: 220px 24px 120px;
  text-align: center;
  background-color: rgba(0, 0, 0, 0.55);
}

.hero-video__brand {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: clamp(2.25rem, 5vw, 3.5rem);
  color: #ffffff;
  letter-spacing: -0.5px;
  margin-bottom: 0.75rem;
  display: inline-flex;
  justify-content: center;
  line-height: 1.1;
}

/* Wave: run L↔R, then pause before repeating */
@keyframes char-run {
  0% {
    transform: translateX(0) translateY(0);
  }
  8% {
    transform: translateX(10px) translateY(-14px);
  }
  16% {
    transform: translateX(0) translateY(0);
  }
  24% {
    transform: translateX(-10px) translateY(-14px);
  }
  32% {
    transform: translateX(0) translateY(0);
  }
  /* Hold still for the rest of the cycle */
  32%,
  100% {
    transform: translateX(0) translateY(0);
  }
}

.hero-video__char {
  display: inline-block;
  will-change: transform;
  animation: char-run 5.5s ease-in-out infinite;
  animation-delay: calc(var(--i) * 0.1s);
}

.hero-video__char.is-accent {
  color: #65F8E4;
}

@media (prefers-reduced-motion: reduce) {
  .hero-video__char {
    animation: none;
  }
}

.hero-video__title {
  color: #65F8E4;
  font-size: clamp(1.25rem, 2.5vw, 1.75rem);
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.hero-video__subtitle {
  color: lightgrey;
  max-width: 36rem;
  margin: 0 auto 1.25rem;
  font-size: 1rem;
  line-height: 1.5;
}
</style>
