<template>
    <div style="width: 100%; background: white;">
        <div v-if="type === 'facebook'">
            <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
            <div
                class="fb-post"
                :data-href="link"
            ></div>
        </div>
        <div v-else-if="type === 'linkedin'">
          <script type="text/javascript" src="https://platform.linkedin.com/badges/js/profile.js" async defer></script>
          <div
              class="badge-base LI-profile-badge"
              data-locale="es_ES"
              data-size="large"
              data-theme="light"
              data-type="HORIZONTAL"
              :data-vanity="extra"
              style="width: 336px; display: inline-block;"
            >
            <a class="LI-simple-link" :href="link + '?trk=profile-badge'"></a>
          </div>
        </div>
    </div>
</template>
<script>
export default {
  layout: 'blank',
  asyncData ({ query }) {
    const fichaDomainParts = query.url.toLowerCase().split('/')
    let type = null
    let extra = null
    if (fichaDomainParts[0] === 'http:' || fichaDomainParts[0] === 'https:') {
      fichaDomainParts.splice(0, 2)
    }
    if (fichaDomainParts[0] === 'www.facebook.com') {
      type = 'facebook'
    } else if (fichaDomainParts[0] === 'www.linkedin.com') {
      type = 'linkedin'
      extra = fichaDomainParts[2]
    }
    return {
      type,
      link: query.url,
      extra
    }
  }
}
</script>
