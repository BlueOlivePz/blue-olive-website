// Zentrale Stammdaten der Seite. Texte und Kontaktdaten nur hier ändern.
export const site = {
  name: 'Blue Olive',
  owner: 'Panajotis Zekos',
  tagline: 'Webdesign & Creative Development',
  url: 'https://blue-olive.online',
  description:
    'Blue Olive ist das Webdesign-Studio von Panajotis Zekos in Ofterdingen: One-Pager, Landingpages, Portfolio- und Agentur-Websites sowie 3D-Scroll-Erlebnisse. Für das Smartphone entworfen, schnell auf jedem Gerät.',
  street: 'Finsterer Weg 13/1',
  zip: '72131',
  city: 'Ofterdingen',
  country: 'Deutschland',
  email: 'info@blue-olive.online',
  phoneDisplay: '0173 3917 911',
  phoneHref: '+491733917911',
  whatsappHref: 'https://wa.me/491733917911',
} as const;

export const nav = [
  { label: 'Leistungen', href: '/#leistungen' },
  { label: 'Arbeiten', href: '/#arbeiten' },
  { label: 'Prozess', href: '/#prozess' },
  { label: 'Wartung', href: '/#wartung' },
  { label: 'Kontakt', href: '/#kontakt' },
] as const;

export const cta = { label: 'Projekt anfragen', href: '/#kontakt' } as const;
