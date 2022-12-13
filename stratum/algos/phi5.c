#include <stdlib.h>
#include <stdint.h>
#include <string.h>
#include <stdio.h>

#include <sha3/sph_bmw.h>
#include <sha3/sph_jh.h>
#include <sha3/sph_keccak.h>
#include <sha3/sph_skein.h>
#include <sha3/sph_cubehash.h>
#include <sha3/sph_echo.h>
#include <sha3/sph_fugue.h>
#include "gost.h"

#include "common.h"

void phi5_hash(const char* input, char* output, uint32_t len)
{
    	sph_skein512_context     ctx_skein;
    	sph_jh512_context        ctx_jh;
    	sph_cubehash512_context  ctx_cubehash;
    	sph_fugue512_context     ctx_fugue;
    	sph_keccak512_context    ctx_keccak;
    	sph_gost512_context      ctx_gost;
    	sph_echo512_context      ctx_echo;
	sph_bmw512_context       ctx_bmw;

	uint8_t _ALIGN(128) hash[64];

	sph_skein512_init(&ctx_skein);
	sph_skein512(&ctx_skein, input, len);
	sph_skein512_close(&ctx_skein, (void*) hash);

	sph_jh512_init(&ctx_jh);
	sph_jh512(&ctx_jh, (const void*) hash, 64);
	sph_jh512_close(&ctx_jh, (void*) hash);

	sph_cubehash512_init(&ctx_cubehash);
	sph_cubehash512(&ctx_cubehash, (const void*) hash, 64);
	sph_cubehash512_close(&ctx_cubehash, (void*) hash);

	sph_fugue512_init(&ctx_fugue);
	sph_fugue512(&ctx_fugue, (const void*) hash, 64);
	sph_fugue512_close(&ctx_fugue, (void*) hash);

	sph_keccak512_init(&ctx_keccak);
	sph_keccak512(&ctx_keccak, (const void*) hash, 64);
	sph_keccak512_close(&ctx_keccak, (void*) hash);

	sph_gost512_init(&ctx_gost);
	sph_gost512(&ctx_gost, (const void*) hash, 64);
	sph_gost512_close(&ctx_gost, (void*) hash);

	sph_echo512_init(&ctx_echo);
	sph_echo512(&ctx_echo, (const void*) hash, 64);
	sph_echo512_close(&ctx_echo, (void*) hash);

	sph_bmw512_init(&ctx_bmw);
	sph_bmw512(&ctx_bmw, (const void*) hash, 64);
	sph_bmw512_close(&ctx_bmw, (void*) hash);

	memcpy(output, hash, 32);
}
