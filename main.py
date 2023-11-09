from transformers import BlenderbotTokenizer, BlenderbotForConditionalGeneration
import sys

mname = "facebook/blenderbot-400M-distill"
model = BlenderbotForConditionalGeneration.from_pretrained(mname)
tokenizer = BlenderbotTokenizer.from_pretrained(mname) 
UTTERANCE = sys.argv[1]
inputs = tokenizer([UTTERANCE], return_tensors="pt")
reply_ids = model.generate(**inputs, max_length=60)
print(tokenizer.batch_decode(reply_ids[0:,1:-1])[0].strip().strip("<s>").strip("</s>"))