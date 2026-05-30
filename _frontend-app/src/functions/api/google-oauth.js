export async function apiGoogleOAuthRedirect() {
  try {
    return await axios.get(window.APP_API_URL + '/google/oauth/redirect', { params: { callback_url: window.APP_GOOGLE_OAUTH_CALLBACK_URL } });
  } catch (error) {
    throw error;
  }
}
export async function apiGoogleOAuthExchangeToken(token) {
  try {
    return await axios.post(window.APP_API_URL + '/google/oauth/exchange/token', null, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
  } catch (error) {
    throw error;
  }
}
