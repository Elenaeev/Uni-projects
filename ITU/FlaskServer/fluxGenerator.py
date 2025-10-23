#author - xdomra00 

import os
from runware import Runware, IImageInference
import time
import asyncio


async def spit_url(prompt):
    if len(prompt) < 3:
        prompt += "..."
    try:
        runware = Runware(api_key=os.getenv('RUNWARE_API_KEY'))
        await (runware.connect())

        request_image = IImageInference(
        positivePrompt=prompt,
        #model="runware:100@1",
        model="civitai:36520@76907",
        numberResults=1,
        height=512,
        width=512,
        seed=999,
        steps=30
        )

        images = await runware.imageInference(requestImage=request_image)

        return images[0].imageURL 
    except Exception as e:
        print(e)

