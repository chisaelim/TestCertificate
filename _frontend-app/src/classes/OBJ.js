export default class OBJ {
    #object;

    static isValidObject(obj) {
        return (
            typeof obj === 'object' &&
            obj !== null &&
            !Array.isArray(obj)
        );
    }

    static isValidKey(key) {
        return (
            typeof key === 'string' ||
            typeof key === 'number' ||
            typeof key === 'symbol'
        );
    }

    constructor(object) {
        if (!OBJ.isValidObject(object)) {
            throw new TypeError('Parameter "object" must be a non-null plain object');
        }
        this.#object = object;
    }
    getObject() {
        return this.#object;
    }

    setObject(object) {
        if (!OBJ.isValidObject(object)) {
            throw new TypeError('Parameter "object" must be a non-null plain object');
        }
        this.#object = object;
    }
    keys() {
        return Object.keys(this.#object);
    }
    values() {
        return Object.values(this.#object);
    }
    toString() {
        return JSON.stringify(this.#object);
    }
    getKeyValue(key) {
        if (!OBJ.isValidKey(key)) {
            throw new TypeError('Key must be a string, number, or symbol');
        }
        return this.#object[key];
    }
    setKeyValue(key, value) {
        if (!OBJ.isValidKey(key)) {
            throw new TypeError('Key must be a string, number, or symbol');
        }
        this.#object[key] = value;
    }
    hasKey(key) {
        if (!OBJ.isValidKey(key)) {
            throw new TypeError('Key must be a string, number, or symbol');
        }
        return Object.prototype.hasOwnProperty.call(this.#object, key);
    }
    deleteKey(key) {
        if (!OBJ.isValidKey(key)) {
            throw new TypeError('Key must be a string, number, or symbol');
        }
        if (this.hasKey(key)) {
            delete this.#object[key];
            return true;
        }
        return false;
    }
    toJSON() {
        return this.#object;
    }
    clone() {
        return new OBJ(JSON.parse(JSON.stringify(this.#object)));
    }
    equals(object2) {
        if (!OBJ.isValidObject(object2)) {
            throw new TypeError('Compare destination parameter "object" must be a non-null plain object');
        }
        if (this.#object === object2) {
            return true;
        }
        const keys1 = Object.keys(this.#object);
        const keys2 = Object.keys(object2);
        if (keys1.length !== keys2.length) {
            return false;
        }
        for (const key of keys1) {
            if (!keys2.includes(key) || !Object.is(this.#object[key], object2[key])) {
                return false;
            }
        }
        return true;
    }
    equalsDeep(object2) {
        if (!OBJ.isValidObject(object2)) {
            throw new TypeError('Compare destination parameter "object" must be a non-null plain object');
        }
        return this._deepEqual(this.#object, object2);
    }

    _deepEqual(obj1, obj2) {
        // Same reference check
        if (obj1 === obj2) {
            return true;
        }

        // Handle null/undefined
        if (obj1 == null || obj2 == null) {
            return obj1 === obj2;
        }

        // Handle different types
        if (typeof obj1 !== typeof obj2) {
            return false;
        }

        // Handle primitive types
        if (typeof obj1 !== 'object') {
            return Object.is(obj1, obj2);
        }

        // Handle arrays
        if (Array.isArray(obj1) !== Array.isArray(obj2)) {
            return false;
        }

        if (Array.isArray(obj1)) {
            if (obj1.length !== obj2.length) {
                return false;
            }
            for (let i = 0; i < obj1.length; i++) {
                if (!this._deepEqual(obj1[i], obj2[i])) {
                    return false;
                }
            }
            return true;
        }

        // Handle objects
        const keys1 = Object.keys(obj1);
        const keys2 = Object.keys(obj2);

        if (keys1.length !== keys2.length) {
            return false;
        }

        for (const key of keys1) {
            if (!keys2.includes(key)) {
                return false;
            }
            if (!this._deepEqual(obj1[key], obj2[key])) {
                return false;
            }
        }

        return true;
    }
    equalKeys(object2) {
        if (!OBJ.isValidObject(object2)) {
            throw new TypeError('Compare destination parameter "object" must be a non-null plain object');
        }
        const keys1 = Object.keys(this.#object);
        const keys2 = Object.keys(object2);
        if (keys1.length !== keys2.length) {
            return false;
        }
        for (const key of keys1) {
            if (!keys2.includes(key)) {
                return false;
            }
        }
        return true;
    }
    equalKeysDeep(object2) {
        if (!OBJ.isValidObject(object2)) {
            throw new TypeError('Compare destination parameter "object" must be a non-null plain object');
        }
        return this._deepKeysEqual(this.#object, object2);
    }

    _deepKeysEqual(obj1, obj2) {
        // Handle null/undefined
        if (obj1 == null || obj2 == null) {
            return obj1 === obj2;
        }

        // Handle different types
        if (typeof obj1 !== typeof obj2) {
            return false;
        }

        // Handle primitive types (no keys to compare)
        if (typeof obj1 !== 'object') {
            return true;
        }

        // Handle arrays
        if (Array.isArray(obj1) !== Array.isArray(obj2)) {
            return false;
        }

        if (Array.isArray(obj1)) {
            if (obj1.length !== obj2.length) {
                return false;
            }
            for (let i = 0; i < obj1.length; i++) {
                if (!this._deepKeysEqual(obj1[i], obj2[i])) {
                    return false;
                }
            }
            return true;
        }

        // Handle objects - compare keys at current level
        const keys1 = Object.keys(obj1);
        const keys2 = Object.keys(obj2);

        if (keys1.length !== keys2.length) {
            return false;
        }

        for (const key of keys1) {
            if (!keys2.includes(key)) {
                return false;
            }
            // Recursively check nested objects
            if (typeof obj1[key] === 'object' && obj1[key] !== null) {
                if (!this._deepKeysEqual(obj1[key], obj2[key])) {
                    return false;
                }
            }
        }

        return true;
    }
    getSameKeys(object2) {
        if (!OBJ.isValidObject(object2)) {
            throw new TypeError('Compare destination parameter "object" must be a non-null plain object');
        }
        const keys1 = Object.keys(this.#object);
        const keys2 = Object.keys(object2);
        const sameKeys = [];
        for (const key of keys1) {
            if (keys2.includes(key)) {
                sameKeys.push(key);
            }
        }
        return sameKeys;
    }
    getDiffKeys(object2, join = '[]') {
        if (!OBJ.isValidObject(object2)) {
            throw new TypeError('Compare destination parameter "object" must be a non-null plain object');
        }
        if (typeof join !== 'string' || !(join === '[' || join === ']' || join === '[]')) {
            throw new TypeError('Parameter second parameter must be "[", "]", or "[]"');
        }
        const keys1 = Object.keys(this.#object);
        const keys2 = Object.keys(object2);
        const diffKeys = [];
        if (join === '[' || join === '[]') {
            for (const key of keys1) {
                if (!keys2.includes(key)) {
                    diffKeys.push(key);
                }
            }
        }
        if (join === ']' || join === '[]') {
            for (const key of keys2) {
                if (!keys1.includes(key)) {
                    diffKeys.push(key);
                }
            }
        }
        return diffKeys;
    }
    omit(keys) {
        if (!Array.isArray(keys)) {
            throw new TypeError('Parameter "keys" must be an array of strings, numbers, or symbols');
        }
        const newObject = { ...this.#object };
        for (const key of keys) {
            if (OBJ.isValidKey(key)) {
                delete newObject[key];
            } else {
                throw new TypeError(`Key "${key}" is not a valid string, number, or symbol`);
            }
        }
        return new OBJ(newObject);
    }
    isEmpty() {
        return Object.keys(this.#object).length === 0;
    }
}

