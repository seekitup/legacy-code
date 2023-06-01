export default function ({ store, redirect, route }) {
  localStorage.lastpath = route.path
  if (!store.state.authUser) {
    return redirect('/login')
  } else {
    store.dispatch('validate_token').catch(() => {
      return redirect('/login')
    })
  }
}
