export default class Photo {
  constructor ({ id, file_name, is_cover, file_size, created_at }) {
    this.id = id
    this.file_name = file_name
    this.file_size = file_size
    this.created_at = created_at
  }
}
