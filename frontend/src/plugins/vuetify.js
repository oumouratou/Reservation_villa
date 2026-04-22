import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'villaTheme',
    themes: {
      villaTheme: {
        dark: false,
        colors: {
          primary: '#696cff',
          secondary: '#8592a3',
          surface: '#ffffff',
          background: '#f5f5f9',
          error: '#ff3e1d',
          success: '#71dd37',
          info: '#00bcd4',
          warning: '#ffb300',
        },
      },
      villaThemeDark: {
        dark: true,
        colors: {
          primary: '#696cff',
          secondary: '#8592a3',
          surface: '#2d2e44',
          background: '#25253b',
          error: '#ff3e1d',
          success: '#71dd37',
          info: '#00bcd4',
          warning: '#ffb300',
        },
      },
    },
  },
})

export default vuetify
