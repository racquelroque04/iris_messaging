$(function () {

  var applicationID = rainbowAppId,
    applicationSecret = rainbowAppSecret
  angular.bootstrap(document, ['sdk']).get('rainbowSDK')

  var onReady = function onReady () {
    var myRainbowLogin = rainbowEmail
    var myRainbowPassword = rainbowPassword

    rainbowSDK.connection.signin(myRainbowLogin, myRainbowPassword)
    setupDevices()
  }

  var onSigned = function onSigned (event, account) {
    console.log('Authentication has been performed successfully. Account information could be retrieved.')
  }

  var onStarted = function onStarted (event, account) {
    console.log('Event started.')
  }

  var onLoaded = function onLoaded () {
    rainbowSDK.setVerboseLog(true)

    rainbowSDK
      .initialize(applicationID, applicationSecret)
      .then(function () {
        console.log('[DEMO] :: Rainbow SDK is initialized!')
        $(document).on(rainbowSDK.RAINBOW_ONREADY, onReady)
      })
      .catch(function (err) {
        console.log('[DEMO] :: Something went wrong with the SDK...', err)
      })
  }

  var onContactPresenceChanged = function onContactPresenceChanged(event, status) {
    console.log("[DEMO] :: Presence changed event");
    switch (status.status) {
      case "online":
        break
      case "offline":
        break
      case "xa":
        break
      case "dnd":
        break
      default:
        break
    }
  }


  var onConnectionStateChangeEvent = function onConnectionStateChangeEvent (event, status) {
    switch (status) {
      case rainbowSDK.connection.RAINBOW_CONNECTIONCONNECTED:
        console.log('Connection connected')
        $('#accept_doctor_consultations').removeAttr("disabled", true);
        $('#connecting_messages').hide()
        break
      case rainbowSDK.connection.RAINBOW_CONNECTIONINPROGRESS:
        console.log('Connection in progress')
        break
      case rainbowSDK.connection.RAINBOW_CONNECTIONCONNECTED:
        console.log('Connection disconnected')
        break
      default:
        break
    }
  }

  var onStopped = function onStopped (event) {
    console.log('The SDK has been completely stopped.')
  }

  $(document).on(rainbowSDK.RAINBOW_ONREADY, onReady)
  $(document).on(rainbowSDK.RAINBOW_ONLOADED, onLoaded)
  $(document).on(rainbowSDK.connection.RAINBOW_ONSIGNED, onSigned)
  $(document).on(rainbowSDK.connection.RAINBOW_ONSTARTED, onStarted)
  $(document).on(rainbowSDK.connection.RAINBOW_ONCONNECTIONSTATECHANGED, onConnectionStateChangeEvent)
  $(document).on(rainbowSDK.presence.RAINBOW_ONCONTACTPRESENCECHANGED, onContactPresenceChanged)
  $(document).on(rainbowSDK.connection.RAINBOW_ONSTOPPED, onStopped)
  rainbowSDK.load()

  function setupDevices () {
    /* Somewhere in your application... Ask the user to authorize the application to access to the media devices */
    navigator.mediaDevices.getUserMedia({audio: true, video: true}).then(function (stream) {
      /* Stream received which means that the user has authorized the application to access to the audio and video devices. Local stream can be stopped at this time */
      stream.getTracks().forEach(function (track) {
        track.stop()
      })

      /*  Get the list of available devices */
      navigator.mediaDevices.enumerateDevices().then(function (devices) {
        /* Do something for each device (e.g. add it to a selector list) */
        var microphoneDevice = null
        var speaker = null
        var camera = null

        devices.forEach(function (device) {
          switch (device.kind) {
            case 'audioinput':
              // This is a device of type 'microphone'
              console.log('[DEMO] :: microphone ID:' + device.deviceId + ' label:' + device.label)
              if (microphoneDevice == null)
                microphoneDevice = device

              break
            case 'audiooutput':
              // This is a device of type 'speaker'
              console.log('[DEMO] :: speaker ID:' + device.deviceId + ' label:' + device.label)
              if (speaker == null)
                speaker = device

              break
            case 'videoinput':
              // This is a device of type 'camera'
              console.log('[DEMO] :: camera ID:' + device.deviceId + ' label:' + device.label)
              if (camera == null)
                camera = device

              break
            default:
              break
          }
        })

        /* Select the microphone to use */
        console.log('[DEMO] :: Set mic to ' + microphoneDevice.deviceId + ' ' + microphoneDevice.label)
        rainbowSDK.webRTC.useMicrophone(microphoneDevice.deviceId)

        /* Select the speaker to use */
        console.log('[DEMO] :: Set speaker to ' + speaker.deviceId + ' ' + speaker.label)
        rainbowSDK.webRTC.useSpeaker(speaker.deviceId)

        /* Select the speaker to use */
        console.log('[DEMO] :: Set camera to ' + camera.deviceId + ' ' + camera.label)
        rainbowSDK.webRTC.useCamera(camera.deviceId)
      }).catch(function (error) {
        /* In case of error when enumerating the devices */
      })
    }).catch(function (error) {
      /* In case of error when authorizing the application to access the media devices */
    })
  }
  var callInVideo = function callInVideo () {
    var contactId = $('#visibility').data('contact')
    rainbowSDK.contacts
      .searchById(contactId)
      .then(function (contact) {
        console.log('CALLING')
        /* Call this API to call a contact using both audio and video streams*/
        var res = rainbowSDK.webRTC.callInVideo(contact)
        if (res.label === 'OK') {
          console.log('Your call has been correctly initiated. Waiting for the other peer to answer')
        }
      })
  }

  var ringtone = new Audio()
  var caller_ringtone = new Audio()

  function playAudio() {
    ringtone.src = RINGTONE
    ringtone.loop = true
    ringtone.play()
  }

  function callerRingtone() {
    caller_ringtone.src = CALLERINGTONE
    caller_ringtone.loop = true
    caller_ringtone.play()
  }

  function pauseAudio() {
    $.each($(ringtone), function () {
      this.pause()
    })
  }

  function callerPausedAudio() {
    $.each($(caller_ringtone), function () {
      this.pause()
      this.currentTime = 0
    })
  }

  var timeOut
  function unAnsweredCall() {
    timeOut = setTimeout(function () {
      rainbowSDK.webRTC.release(call)
      $('#answer-call').modal('hide')
      callerPausedAudio()
    },30000)
  }

  function stopTimeOut() {
    clearTimeout(timeOut)

  }

  var onWebRTCCallChanged = function onWebRTCCallChanged (event, call) {

    if (call.status.value === 'Unknown') {
      callerPausedAudio()
      pauseAudio()
      unAnsweredCall()
      stopTimeOut()
    }
    if (call.status.value === 'active') {
      rainbowSDK.webRTC.showLocalVideo()
      callerPausedAudio()
      rainbowSDK.webRTC.showRemoteVideo(call)
    }
    if (call.status.value === 'dialing') {
      console.clear()
      callerRingtone()
    }

    var endVideoCall = document.getElementById('endVideoCall')
    endVideoCall.onclick = function () {
      console.log('call ended')
      rainbowSDK.webRTC.release(call)
      callerPausedAudio()
    }

    var declineVideoCall = document.getElementById('declineVideoCall')
    declineVideoCall.onclick = function () {
      console.log('decline call')
      rainbowSDK.webRTC.release(call)
    }

    var closeWindow = document.getElementById('closeWindow')
    closeWindow.onclick = function () {
      console.log('close window')
      rainbowSDK.webRTC.release(call)
    }

    /* Listen to WebRTC call state change */
    if (call.status.value === 'incommingCall') {
      console.clear()
      playAudio()
      if (call.remoteMedia === 3) {
        var answerVideoCall = document.getElementById('answerVideoCall')
        answerVideoCall.onclick = function () {
          rainbowSDK.webRTC.answerInVideo(call)
          pauseAudio()
        }
      }
    }
  }
  $(document).on(rainbowSDK.webRTC.RAINBOW_ONWEBRTCCALLSTATECHANGED, onWebRTCCallChanged)
})
