(() => {
  const shareMap = {
    facebook: (url) => `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
    x: (url) => `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}`,
    whatsapp: (url) => `https://wa.me/?text=${encodeURIComponent(url)}`
  };

  document.querySelectorAll('.js-share').forEach((btn) => {
    btn.addEventListener('click', () => {
      const url = btn.dataset.url || window.location.href;
      const network = btn.dataset.network;
      const target = shareMap[network];
      if (target) window.open(target(url), '_blank', 'noopener,noreferrer,width=600,height=480');
    });
  });

  document.querySelectorAll('.js-post-like').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const body = new URLSearchParams({
        action: 'berita_lite_post_like',
        nonce: beritaLite.nonce,
        postId: btn.dataset.post || ''
      });
      const res = await fetch(beritaLite.ajaxUrl, { method: 'POST', body });
      const json = await res.json();
      if (json?.success) {
        const count = btn.querySelector('.js-like-count');
        if (count) count.textContent = json.data.likes;
      }
    });
  });

  document.querySelectorAll('.js-comment-reaction').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const body = new URLSearchParams({
        action: 'berita_lite_comment_reaction',
        nonce: beritaLite.nonce,
        commentId: btn.dataset.comment || '',
        emoji: btn.dataset.emoji || '👍'
      });
      const res = await fetch(beritaLite.ajaxUrl, { method: 'POST', body });
      const json = await res.json();
      if (json?.success) {
        const count = btn.querySelector('.js-comment-count');
        if (count) count.textContent = json.data.count;
      }
    });
  });

  const modal = document.querySelector('.js-author-modal');
  const open = document.querySelector('.js-author-modal-open');
  const close = document.querySelector('.js-author-modal-close');
  if (modal && open && close) {
    open.addEventListener('click', () => modal.classList.add('is-open'));
    close.addEventListener('click', () => modal.classList.remove('is-open'));
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.remove('is-open');
    });
  }
})();

