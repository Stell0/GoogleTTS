# GoogleTTS
Quick and dirty script to use Google TTS APIs to generate mp3 audio from *long as you want* text file

## How to install

Launch
```
composer install
```

Configure a Google application for Text To Speech and copy the authentication json file into `google-auth.json`

Also Sox is required, launch
```
apt install sox
```

## How to use

Change language and voice in tts.php if you whant to

Put the text in a text file, then launch

```
php tts.php text_file
```

A text_file.mp3 will be generated
