(function () {
    console.log('tlt script loaded');
      
    const entryKey = 'telegram_bot_link';
  
    if (!localStorage.getItem(entryKey)) {
      localStorage.setItem(entryKey, window.location.href);
      console.log('Saved initial link to localStorage:', window.location.href);
    }
  
    function updateTelegramLinks(shortId) {
      const tgLink = `https://t.me/geteroticabot?start=${shortId}`;
      const buttons = document.querySelectorAll('.chat-button__link.chat-button__link-tg');
      
      if (buttons.length === 0) {
        console.warn('No Telegram buttons found on the page');
      }
  
      buttons.forEach(btn => {
        console.log('Updating Telegram link to:', tgLink);
        btn.href = tgLink;
      });
    }
  
    document.addEventListener("DOMContentLoaded", () => {
      const saved = localStorage.getItem(entryKey);
      if (saved) {
        console.log('Loaded saved URL:', saved);
  
        fetch('/wp-json/tlt/v1/generate/', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ full_url: saved })
        })
        .then(res => res.json())
        .then(data => {
          console.log('Received short_id from API:', data.short_id);
          if (data.short_id) {
            updateTelegramLinks(data.short_id);
          } else {
            console.warn('No short_id returned from server', data);
          }
        })
        .catch(err => {
          console.error('Error generating link:', err);
        });
      }
    });
  })();