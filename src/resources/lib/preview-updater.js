const arrayBufferToBase64 = buffer => {
  let binary = '';

  [].slice.call(new Uint8Array(buffer)).forEach(b => binary += String.fromCharCode(b));

  return window.btoa(binary);
};

const createPreviewUpdater = (previewUrl, imageElement, csrfTokenName, csrfTokenValue, callback = (values, isFinished) => {}) => {
  let nextRequest = null;
  let requestInProgress = false;

  const updateImage = base64String => {
    imageElement.src = 'data:image/png;base64,' + base64String;
  };

  const makeRequest = async (values) => {
    if (requestInProgress) {
      nextRequest = values;

      return;
    }

    requestInProgress = true;

    callback(values, false);

    const body = new URLSearchParams();

    body.append(csrfTokenName, csrfTokenValue);
    Object.keys(values).forEach(key => body.append(key, values[key]));

    const response = await fetch(previewUrl, {
      method: 'post',
      credentials: 'same-origin',
      body,
    });

    const buffer = await response.arrayBuffer();
    const imageString = arrayBufferToBase64(buffer);

    updateImage(imageString);
    callback(values, nextRequest === null);

    requestInProgress = false;

    if (nextRequest === null) {
      return;
    }

    makeRequest(nextRequest);
    nextRequest = null;
  };

  return makeRequest;
};

export default createPreviewUpdater;